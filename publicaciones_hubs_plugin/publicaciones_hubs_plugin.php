<?php
/**
 * Plugin Name: Publicaciones Hubs
 * Description: Plugin para mostrar publicaciones de Hubs migradas desde Drupal
 * Version: 1.0
 * Author: Gonzalo Bianchi
 * Text Domain: publicaciones-hubs
 * Domain Path: /languages
 */

// Prevenir acceso directo
if (!defined('ABSPATH')) {
    exit;
}

// Definir constantes del plugin
define('PUBLICACIONES_HUBS_VERSION', '1.0');
define('PUBLICACIONES_HUBS_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('PUBLICACIONES_HUBS_PLUGIN_URL', plugin_dir_url(__FILE__));

class PublicacionesHubsPlugin {
    
    public function __construct() {
        add_action('init', array($this, 'init'));
        add_action('add_meta_boxes', array($this, 'add_hub_meta_boxes'));
        add_action('save_post', array($this, 'save_hub_meta_fields'));
        
        // NUEVO: Hook para interceptar templates de taxonomías
        add_filter('template_include', array($this, 'custom_taxonomy_template'));
        
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
    }
    
    /**
     * Inicializar el plugin
     */
    public function init() {
        // Registrar custom post type
        $this->register_post_type();
        
        // Registrar taxonomías
        $this->register_taxonomies();
        
        // Cargar textdomain para traducciones
        load_plugin_textdomain('publicaciones-hubs', false, dirname(plugin_basename(__FILE__)) . '/languages/');
    }
    
    /**
     * NUEVO: Interceptar templates de taxonomías para usar nuestros propios
     */
    public function custom_taxonomy_template($template) {
        // Solo actuar en taxonomías de hubs
        if (is_tax('category_hub') || is_tax('tag_hub')) {
            
            // Para categorías de hub
            if (is_tax('category_hub')) {
                $term = get_queried_object();
                
                // Establecer variables globales para compatibilidad
                $GLOBALS['hub_category'] = $term;
                $GLOBALS['is_hub_category_archive'] = true;
                
                // Buscar templates en orden de prioridad
                $templates = array(
                    "archive-hubs-category-{$term->slug}.php",
                    "archive-hubs-category.php",
                    "taxonomy-category_hub-{$term->slug}.php",
                    "taxonomy-category_hub.php",
                    "archive-hubs.php",
                    "taxonomy.php",
                    "archive.php",
                    "index.php"
                );
                
                $custom_template = locate_template($templates);
                
                if ($custom_template) {
                    return $custom_template;
                } else {
                    // Si no hay template personalizado, usar el nuestro por defecto
                    return $this->load_default_category_template();
                }
            }
            
            // Para tags de hub
            if (is_tax('tag_hub')) {
                $term = get_queried_object();
                
                $GLOBALS['hub_tag'] = $term;
                $GLOBALS['is_hub_tag_archive'] = true;
                
                $templates = array(
                    "archive-hubs-tag-{$term->slug}.php",
                    "archive-hubs-tag.php",
                    "taxonomy-tag_hub-{$term->slug}.php",
                    "taxonomy-tag_hub.php",
                    "archive-hubs.php",
                    "taxonomy.php",
                    "archive.php",
                    "index.php"
                );
                
                $custom_template = locate_template($templates);
                
                if ($custom_template) {
                    return $custom_template;
                }
            }
        }
        
        return $template;
    }
    
    /**
     * Template por defecto cuando no se encuentra uno personalizado
     */
    private function load_default_category_template() {
        // Crear un archivo temporal para el template por defecto
        $temp_template = PUBLICACIONES_HUBS_PLUGIN_DIR . 'default-category-template.php';
        
        if (!file_exists($temp_template)) {
            $content = $this->get_default_template_content();
            file_put_contents($temp_template, $content);
        }
        
        return $temp_template;
    }
    
    /**
     * Contenido del template por defecto
     */
    private function get_default_template_content() {
        return '<?php
/**
 * Template por defecto para categorías de hubs
 */
get_header(); 

$current_category = get_queried_object();
?>

<!-- BANNER DE CONFIRMACIÓN SIMPLE -->
<div style="background: linear-gradient(45deg, #4CAF50, #45a049); color: white; padding: 20px; text-align: center; margin: 20px; border-radius: 10px;">
    <h1 style="margin: 0;">✅ TEMPLATE NATIVO FUNCIONANDO</h1>
    <p style="margin: 10px 0 0 0;">
        <strong>Categoría:</strong> <?php echo esc_html($current_category->name); ?><br>
        <strong>Slug:</strong> <?php echo esc_html($current_category->slug); ?><br>
        <strong>Posts:</strong> <?php echo $current_category->count; ?>
    </p>
</div>

<div class="container">
    <h1>Hubs de <?php echo esc_html($current_category->name); ?></h1>
    
    <?php if (have_posts()) : ?>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; margin: 20px 0;">
            <?php while (have_posts()) : the_post(); ?>
                <article style="border: 1px solid #ddd; padding: 20px; border-radius: 8px; background: white; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                    
                    <?php if (has_post_thumbnail()) : ?>
                        <div style="margin-bottom: 15px;">
                            <a href="<?php the_permalink(); ?>">
                                <?php the_post_thumbnail("medium", array("style" => "width: 100%; height: 200px; object-fit: cover; border-radius: 5px;")); ?>
                            </a>
                        </div>
                    <?php endif; ?>
                    
                    <div>
                        <?php if (has_hub_volanta()) : ?>
                            <div style="color: #4CAF50; font-size: 0.85rem; font-weight: bold; text-transform: uppercase; margin-bottom: 8px;">
                                <?php the_hub_volanta(); ?>
                            </div>
                        <?php endif; ?>
                        
                        <h2 style="margin: 0 0 10px 0; font-size: 1.3rem;">
                            <a href="<?php the_permalink(); ?>" style="color: #333; text-decoration: none;">
                                <?php the_title(); ?>
                            </a>
                        </h2>
                        
                        <?php if (has_excerpt()) : ?>
                            <div style="color: #666; line-height: 1.5; margin-bottom: 15px;">
                                <?php the_excerpt(); ?>
                            </div>
                        <?php endif; ?>
                        
                        <div style="font-size: 0.9rem; color: #888; border-top: 1px solid #eee; padding-top: 10px;">
                            <span><?php echo get_the_date(); ?></span> | 
                            <span>por <?php the_author(); ?></span>
                        </div>
                    </div>
                </article>
            <?php endwhile; ?>
        </div>
        
        <div style="text-align: center; margin-top: 30px;">
            <?php
            the_posts_pagination(array(
                "mid_size" => 2,
                "prev_text" => "← Anterior",
                "next_text" => "Siguiente →",
            ));
            ?>
        </div>
        
    <?php else : ?>
        <div style="text-align: center; padding: 40px; background: #f9f9f9; border-radius: 10px;">
            <h3>No hay hubs en esta categoría</h3>
            <p>Intenta con otra categoría o vuelve más tarde.</p>
        </div>
    <?php endif; ?>
</div>

<?php get_footer(); ?>';
    }
    
    /**
     * Registrar el custom post type 'hubs'
     */
    public function register_post_type() {
        $labels = array(
            'name'                  => _x('Hubs', 'Post Type General Name', 'publicaciones-hubs'),
            'singular_name'         => _x('Hub', 'Post Type Singular Name', 'publicaciones-hubs'),
            'menu_name'             => __('Hubs', 'publicaciones-hubs'),
            'name_admin_bar'        => __('Hub', 'publicaciones-hubs'),
            'archives'              => __('Archivo de Hubs', 'publicaciones-hubs'),
            'attributes'            => __('Atributos del Hub', 'publicaciones-hubs'),
            'parent_item_colon'     => __('Hub Padre:', 'publicaciones-hubs'),
            'all_items'             => __('Todos los Hubs', 'publicaciones-hubs'),
            'add_new_item'          => __('Agregar Nuevo Hub', 'publicaciones-hubs'),
            'add_new'               => __('Agregar Nuevo', 'publicaciones-hubs'),
            'new_item'              => __('Nuevo Hub', 'publicaciones-hubs'),
            'edit_item'             => __('Editar Hub', 'publicaciones-hubs'),
            'update_item'           => __('Actualizar Hub', 'publicaciones-hubs'),
            'view_item'             => __('Ver Hub', 'publicaciones-hubs'),
            'view_items'            => __('Ver Hubs', 'publicaciones-hubs'),
            'search_items'          => __('Buscar Hubs', 'publicaciones-hubs'),
            'not_found'             => __('No se encontraron Hubs', 'publicaciones-hubs'),
            'not_found_in_trash'    => __('No se encontraron Hubs en la papelera', 'publicaciones-hubs'),
            'featured_image'        => __('Imagen Destacada', 'publicaciones-hubs'),
            'set_featured_image'    => __('Establecer imagen destacada', 'publicaciones-hubs'),
            'remove_featured_image' => __('Quitar imagen destacada', 'publicaciones-hubs'),
            'use_featured_image'    => __('Usar como imagen destacada', 'publicaciones-hubs'),
            'insert_into_item'      => __('Insertar en Hub', 'publicaciones-hubs'),
            'uploaded_to_this_item' => __('Subido a este Hub', 'publicaciones-hubs'),
            'items_list'            => __('Lista de Hubs', 'publicaciones-hubs'),
            'items_list_navigation' => __('Navegación de lista de Hubs', 'publicaciones-hubs'),
            'filter_items_list'     => __('Filtrar lista de Hubs', 'publicaciones-hubs'),
        );
        
        $args = array(
            'label'                 => __('Hub', 'publicaciones-hubs'),
            'description'           => __('Publicaciones de Hubs migradas desde Drupal', 'publicaciones-hubs'),
            'labels'                => $labels,
            'supports'              => array('title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'trackbacks', 'revisions', 'custom-fields'),
            'taxonomies'            => array('category_hub', 'tag_hub'),
            'hierarchical'          => false,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'menu_position'         => 5,
            'menu_icon'             => 'dashicons-networking',
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => true,
            'can_export'            => true,
            'has_archive'           => 'hubs',
            'exclude_from_search'   => false,
            'publicly_queryable'    => true,
            'rewrite'               => array(
                'slug' => 'hub',
                'with_front' => false,
                'pages' => true,
                'feeds' => true,
            ),
            'capability_type'       => 'post',
            'show_in_rest'          => true,
            'rest_base'             => 'hubs',
            'rest_controller_class' => 'WP_REST_Posts_Controller',
        );
        
        register_post_type('hubs', $args);
    }
    
    /**
     * Registrar taxonomías personalizadas para hubs
     */
    public function register_taxonomies() {
        // Registrar taxonomía de categorías para hubs
        $this->register_category_hub_taxonomy();
        
        // Registrar taxonomía de tags para hubs
        $this->register_tag_hub_taxonomy();
    }
    
    /**
     * Registrar taxonomía category_hub
     */
    private function register_category_hub_taxonomy() {
        $labels = array(
            'name'                       => _x('Categorías Hub', 'Taxonomy General Name', 'publicaciones-hubs'),
            'singular_name'              => _x('Categoría Hub', 'Taxonomy Singular Name', 'publicaciones-hubs'),
            'menu_name'                  => __('Categorías Hub', 'publicaciones-hubs'),
            'all_items'                  => __('Todas las Categorías Hub', 'publicaciones-hubs'),
            'parent_item'                => __('Categoría Hub Padre', 'publicaciones-hubs'),
            'parent_item_colon'          => __('Categoría Hub Padre:', 'publicaciones-hubs'),
            'new_item_name'              => __('Nuevo Nombre de Categoría Hub', 'publicaciones-hubs'),
            'add_new_item'               => __('Agregar Nueva Categoría Hub', 'publicaciones-hubs'),
            'edit_item'                  => __('Editar Categoría Hub', 'publicaciones-hubs'),
            'update_item'                => __('Actualizar Categoría Hub', 'publicaciones-hubs'),
            'view_item'                  => __('Ver Categoría Hub', 'publicaciones-hubs'),
            'separate_items_with_commas' => __('Separar categorías con comas', 'publicaciones-hubs'),
            'add_or_remove_items'        => __('Agregar o quitar categorías', 'publicaciones-hubs'),
            'choose_from_most_used'      => __('Elegir de las más usadas', 'publicaciones-hubs'),
            'popular_items'              => __('Categorías Populares', 'publicaciones-hubs'),
            'search_items'               => __('Buscar Categorías', 'publicaciones-hubs'),
            'not_found'                  => __('No se encontraron categorías', 'publicaciones-hubs'),
            'no_terms'                   => __('No hay categorías', 'publicaciones-hubs'),
            'items_list'                 => __('Lista de categorías', 'publicaciones-hubs'),
            'items_list_navigation'      => __('Navegación de lista de categorías', 'publicaciones-hubs'),
        );
        
        $args = array(
            'labels'                     => $labels,
            'hierarchical'               => true,
            'public'                     => true,
            'show_ui'                    => true,
            'show_admin_column'          => true,
            'show_in_nav_menus'          => true,
            'show_tagcloud'              => true,
            'show_in_rest'               => true,
            'rest_base'                  => 'category_hub',
            'rest_controller_class'      => 'WP_REST_Terms_Controller',
            'rewrite'                    => array(
                'slug' => 'hubs/category',
                'with_front' => false,
                'hierarchical' => true,
            ),
        );
        
        register_taxonomy('category_hub', array('hubs'), $args);
    }
    
    /**
     * Registrar taxonomía tag_hub
     */
    private function register_tag_hub_taxonomy() {
        $labels = array(
            'name'                       => _x('Tags Hub', 'Taxonomy General Name', 'publicaciones-hubs'),
            'singular_name'              => _x('Tag Hub', 'Taxonomy Singular Name', 'publicaciones-hubs'),
            'menu_name'                  => __('Tags Hub', 'publicaciones-hubs'),
            'all_items'                  => __('Todos los Tags Hub', 'publicaciones-hubs'),
            'parent_item'                => __('Tag Hub Padre', 'publicaciones-hubs'),
            'parent_item_colon'          => __('Tag Hub Padre:', 'publicaciones-hubs'),
            'new_item_name'              => __('Nuevo Nombre de Tag Hub', 'publicaciones-hubs'),
            'add_new_item'               => __('Agregar Nuevo Tag Hub', 'publicaciones-hubs'),
            'edit_item'                  => __('Editar Tag Hub', 'publicaciones-hubs'),
            'update_item'                => __('Actualizar Tag Hub', 'publicaciones-hubs'),
            'view_item'                  => __('Ver Tag Hub', 'publicaciones-hubs'),
            'separate_items_with_commas' => __('Separar tags con comas', 'publicaciones-hubs'),
            'add_or_remove_items'        => __('Agregar o quitar tags', 'publicaciones-hubs'),
            'choose_from_most_used'      => __('Elegir de los más usados', 'publicaciones-hubs'),
            'popular_items'              => __('Tags Populares', 'publicaciones-hubs'),
            'search_items'               => __('Buscar Tags', 'publicaciones-hubs'),
            'not_found'                  => __('No se encontraron tags', 'publicaciones-hubs'),
            'no_terms'                   => __('No hay tags', 'publicaciones-hubs'),
            'items_list'                 => __('Lista de tags', 'publicaciones-hubs'),
            'items_list_navigation'      => __('Navegación de lista de tags', 'publicaciones-hubs'),
        );
        
        $args = array(
            'labels'                     => $labels,
            'hierarchical'               => false,
            'public'                     => true,
            'show_ui'                    => true,
            'show_admin_column'          => true,
            'show_in_nav_menus'          => true,
            'show_tagcloud'              => true,
            'show_in_rest'               => true,
            'rest_base'                  => 'tag_hub',
            'rest_controller_class'      => 'WP_REST_Terms_Controller',
            'rewrite'                    => array(
                'slug' => 'hubs/tag',
                'with_front' => false,
            ),
        );
        
        register_taxonomy('tag_hub', array('hubs'), $args);
    }
    
    /**
     * Agregar meta boxes para campos personalizados
     */
    public function add_hub_meta_boxes() {
        add_meta_box(
            'hub_volanta_meta_box',
            __('Volanta del Hub', 'publicaciones-hubs'),
            array($this, 'hub_volanta_meta_box_callback'),
            'hubs',
            'normal',
            'high'
        );
    }
    
    /**
     * Callback para renderizar el meta box de volanta
     */
    public function hub_volanta_meta_box_callback($post) {
        wp_nonce_field('hub_volanta_nonce_action', 'hub_volanta_nonce');
        
        $volanta = get_post_meta($post->ID, '_hub_volanta', true);
        
        ?>
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="hub_volanta"><?php _e('Volanta', 'publicaciones-hubs'); ?></label>
                </th>
                <td>
                    <input type="text" 
                           id="hub_volanta" 
                           name="hub_volanta" 
                           value="<?php echo esc_attr($volanta); ?>" 
                           class="regular-text" 
                           placeholder="<?php _e('Ingrese la volanta del hub...', 'publicaciones-hubs'); ?>" />
                    <p class="description">
                        <?php _e('Texto breve que aparece sobre el título del hub (opcional).', 'publicaciones-hubs'); ?>
                    </p>
                </td>
            </tr>
        </table>
        
        <style>
        #hub_volanta_meta_box .form-table th {
            width: 120px;
            font-weight: 600;
        }
        #hub_volanta_meta_box .regular-text {
            width: 100%;
            max-width: 500px;
        }
        #hub_volanta_meta_box .description {
            margin-top: 5px;
            font-style: italic;
            color: #666;
        }
        </style>
        <?php
    }
    
    /**
     * Guardar campos meta personalizados
     */
    public function save_hub_meta_fields($post_id) {
        if (!isset($_POST['hub_volanta_nonce']) || !wp_verify_nonce($_POST['hub_volanta_nonce'], 'hub_volanta_nonce_action')) {
            return;
        }
        
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        
        if (get_post_type($post_id) !== 'hubs') {
            return;
        }
        
        if (isset($_POST['hub_volanta'])) {
            $volanta = sanitize_text_field($_POST['hub_volanta']);
            update_post_meta($post_id, '_hub_volanta', $volanta);
        }
    }
    
    /**
     * Activar el plugin
     */
    public function activate() {
        $this->register_post_type();
        $this->register_taxonomies();
        flush_rewrite_rules();
    }
    
    /**
     * Desactivar el plugin
     */
    public function deactivate() {
        flush_rewrite_rules();
    }
}

// Inicializar el plugin
new PublicacionesHubsPlugin();

/**
 * FUNCIONES HELPER PARA USAR EN TEMPLATES
 */

/**
 * Función para obtener hubs
 */
function get_hubs($args = array()) {
    $default_args = array(
        'post_type' => 'hubs',
        'post_status' => 'publish'
    );
    
    $args = wp_parse_args($args, $default_args);
    return get_posts($args);
}

/**
 * Función para obtener categorías de hub
 */
function get_hub_categories($args = array()) {
    $default_args = array(
        'taxonomy' => 'category_hub',
        'hide_empty' => false
    );
    
    $args = wp_parse_args($args, $default_args);
    return get_terms($args);
}

/**
 * Función para obtener tags de hub
 */
function get_hub_tags($args = array()) {
    $default_args = array(
        'taxonomy' => 'tag_hub',
        'hide_empty' => false
    );
    
    $args = wp_parse_args($args, $default_args);
    return get_terms($args);
}

/**
 * Función para verificar si un post es un hub
 */
function is_hub_post($post = null) {
    if (!$post) {
        $post = get_post();
    }
    
    if (!$post) {
        return false;
    }
    
    return $post->post_type === 'hubs';
}

/**
 * Función para obtener la URL del archivo de hubs
 */
function get_hubs_archive_url() {
    return get_post_type_archive_link('hubs');
}

/**
 * Función para obtener la URL del archivo de una categoría específica de hub
 */
function get_hub_category_archive_url($category_slug) {
    return home_url("/hubs/category/{$category_slug}/");
}

/**
 * Función para obtener la URL del archivo de un tag específico de hub
 */
function get_hub_tag_archive_url($tag_slug) {
    return home_url("/hubs/tag/{$tag_slug}/");
}

/**
 * Función para verificar si estamos en un archivo de categoría de hub
 */
function is_hub_category_archive() {
    return is_tax('category_hub') || (isset($GLOBALS['is_hub_category_archive']) && $GLOBALS['is_hub_category_archive']);
}

/**
 * Función para verificar si estamos en un archivo de tag de hub
 */
function is_hub_tag_archive() {
    return is_tax('tag_hub') || (isset($GLOBALS['is_hub_tag_archive']) && $GLOBALS['is_hub_tag_archive']);
}

/**
 * Función para obtener la categoría actual del archivo de hub
 */
function get_current_hub_category() {
    if (is_tax('category_hub')) {
        return get_queried_object();
    }
    return isset($GLOBALS['hub_category']) ? $GLOBALS['hub_category'] : null;
}

/**
 * Función para obtener el tag actual del archivo de hub
 */
function get_current_hub_tag() {
    if (is_tax('tag_hub')) {
        return get_queried_object();
    }
    return isset($GLOBALS['hub_tag']) ? $GLOBALS['hub_tag'] : null;
}

/**
 * Función para obtener la volanta de un hub
 */
function get_hub_volanta($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    return get_post_meta($post_id, '_hub_volanta', true);
}

/**
 * Función para mostrar la volanta de un hub
 */
function the_hub_volanta($post_id = null) {
    $volanta = get_hub_volanta($post_id);
    if (!empty($volanta)) {
        echo esc_html($volanta);
    }
}

/**
 * Función para verificar si un hub tiene volanta
 */
function has_hub_volanta($post_id = null) {
    $volanta = get_hub_volanta($post_id);
    return !empty($volanta);
}

/**
 * Función para mostrar la volanta con HTML wrapper
 */
function display_hub_volanta($post_id = null, $wrapper_class = 'hub-volanta') {
    $volanta = get_hub_volanta($post_id);
    if (!empty($volanta)) {
        return '<div class="' . esc_attr($wrapper_class) . '">' . esc_html($volanta) . '</div>';
    }
    return '';
}

/**
 * Función para obtener hubs de una categoría específica
 */
function get_hubs_by_category($category_slug, $args = array()) {
    $default_args = array(
        'post_type' => 'hubs',
        'post_status' => 'publish',
        'posts_per_page' => 10,
        'tax_query' => array(
            array(
                'taxonomy' => 'category_hub',
                'field' => 'slug',
                'terms' => $category_slug
            )
        )
    );
    
    $args = wp_parse_args($args, $default_args);
    return get_posts($args);
}

/**
 * Función para obtener hubs por tag
 */
function get_hubs_by_tag($tag_slug, $args = array()) {
    $default_args = array(
        'post_type' => 'hubs',
        'post_status' => 'publish',
        'tax_query' => array(
            array(
                'taxonomy' => 'tag_hub',
                'field' => 'slug',
                'terms' => $tag_slug
            )
        )
    );
    
    $args = wp_parse_args($args, $default_args);
    return get_posts($args);
}

/**
 * Función para obtener las categorías de un hub específico
 */
function get_hub_post_categories($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    return get_the_terms($post_id, 'category_hub');
}

/**
 * Función para obtener los tags de un hub específico
 */
function get_hub_post_tags($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    return get_the_terms($post_id, 'tag_hub');
}

/**
 * Función para mostrar las categorías de un hub
 */
function the_hub_categories($separator = ', ', $post_id = null) {
    $categories = get_hub_post_categories($post_id);
    
    if (!$categories || is_wp_error($categories)) {
        return;
    }
    
    $category_links = array();
    foreach ($categories as $category) {
        $category_links[] = '<a href="' . get_hub_category_archive_url($category->slug) . '">' . esc_html($category->name) . '</a>';
    }
    
    echo implode($separator, $category_links);
}

/**
 * Función para mostrar los tags de un hub
 */
function the_hub_tags($separator = ', ', $post_id = null) {
    $tags = get_hub_post_tags($post_id);
    
    if (!$tags || is_wp_error($tags)) {
        return;
    }
    
    $tag_links = array();
    foreach ($tags as $tag) {
        $tag_links[] = '<a href="' . get_hub_tag_archive_url($tag->slug) . '">' . esc_html($tag->name) . '</a>';
    }
    
    echo implode($separator, $tag_links);
}

/**
 * Función para obtener hubs relacionados por categoría
 */
function get_related_hubs($post_id = null, $limit = 5) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    $categories = get_hub_post_categories($post_id);
    
    if (!$categories || is_wp_error($categories)) {
        return array();
    }
    
    $category_ids = wp_list_pluck($categories, 'term_id');
    
    $args = array(
        'post_type' => 'hubs',
        'post_status' => 'publish',
        'posts_per_page' => $limit,
        'post__not_in' => array($post_id),
        'tax_query' => array(
            array(
                'taxonomy' => 'category_hub',
                'field' => 'term_id',
                'terms' => $category_ids,
                'operator' => 'IN'
            )
        ),
        'orderby' => 'rand'
    );
    
    return get_posts($args);
}

/**
 * Función para obtener el breadcrumb de hubs
 */
function get_hub_breadcrumb($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    $breadcrumb = array();
    
    // Agregar enlace a home
    $breadcrumb[] = '<a href="' . home_url() . '">Inicio</a>';
    
    // Agregar enlace al archivo de hubs
    $breadcrumb[] = '<a href="' . get_hubs_archive_url() . '">Hubs</a>';
    
    // Si estamos en un post específico, agregar categorías
    if (is_singular('hubs')) {
        $categories = get_hub_post_categories($post_id);
        if ($categories && !is_wp_error($categories)) {
            $main_category = $categories[0]; // Tomar la primera categoría
            $breadcrumb[] = '<a href="' . get_hub_category_archive_url($main_category->slug) . '">' . esc_html($main_category->name) . '</a>';
        }
        
        $breadcrumb[] = '<span class="current">' . get_the_title($post_id) . '</span>';
    }
    
    return implode(' &raquo; ', $breadcrumb);
}

/**
 * Función para mostrar el breadcrumb de hubs
 */
function the_hub_breadcrumb($post_id = null) {
    echo get_hub_breadcrumb($post_id);
}

/**
 * Función para obtener hubs recientes
 */
function get_recent_hubs($limit = 5) {
    $args = array(
        'post_type' => 'hubs',
        'post_status' => 'publish',
        'posts_per_page' => $limit,
        'orderby' => 'date',
        'order' => 'DESC'
    );
    
    return get_posts($args);
}

/**
 * Función para obtener hubs destacados (con imagen destacada)
 */
function get_featured_hubs($limit = 5) {
    $args = array(
        'post_type' => 'hubs',
        'post_status' => 'publish',
        'posts_per_page' => $limit,
        'meta_query' => array(
            array(
                'key' => '_thumbnail_id',
                'compare' => 'EXISTS'
            )
        ),
        'orderby' => 'date',
        'order' => 'DESC'
    );
    
    return get_posts($args);
}

/**
 * Función para obtener estadísticas de hubs
 */
function get_hub_stats() {
    $stats = array();
    
    // Total de hubs
    $stats['total_hubs'] = wp_count_posts('hubs')->publish;
    
    // Total de categorías
    $stats['total_categories'] = wp_count_terms(array(
        'taxonomy' => 'category_hub',
        'hide_empty' => false
    ));
    
    // Total de tags
    $stats['total_tags'] = wp_count_terms(array(
        'taxonomy' => 'tag_hub',
        'hide_empty' => false
    ));
    
    return $stats;
}

/**
 * Función para obtener el widget de hubs populares
 */
function get_popular_hubs_widget($limit = 5, $days = 30) {
    $args = array(
        'post_type' => 'hubs',
        'post_status' => 'publish',
        'posts_per_page' => $limit,
        'meta_key' => 'post_views_count',
        'orderby' => 'meta_value_num',
        'order' => 'DESC',
        'date_query' => array(
            array(
                'after' => $days . ' days ago'
            )
        )
    );
    
    return get_posts($args);
}

/**
 * Función para mostrar un widget de hubs recientes
 */
function display_recent_hubs_widget($title = 'Hubs Recientes', $limit = 5) {
    $hubs = get_recent_hubs($limit);
    
    if (empty($hubs)) {
        return;
    }
    
    echo '<div class="recent-hubs-widget">';
    echo '<h3>' . esc_html($title) . '</h3>';
    echo '<ul>';
    
    foreach ($hubs as $hub) {
        echo '<li>';
        echo '<a href="' . get_permalink($hub->ID) . '">' . esc_html($hub->post_title) . '</a>';
        echo '<span class="hub-date">' . get_the_date('', $hub->ID) . '</span>';
        echo '</li>';
    }
    
    echo '</ul>';
    echo '</div>';
}

/**
 * Función para obtener hubs por autor
 */
function get_hubs_by_author($author_id, $limit = 10) {
    $args = array(
        'post_type' => 'hubs',
        'post_status' => 'publish',
        'author' => $author_id,
        'posts_per_page' => $limit,
        'orderby' => 'date',
        'order' => 'DESC'
    );
    
    return get_posts($args);
}

/**
 * Función para buscar hubs
 */
function search_hubs($search_term, $limit = 10) {
    $args = array(
        'post_type' => 'hubs',
        'post_status' => 'publish',
        's' => $search_term,
        'posts_per_page' => $limit
    );
    
    return get_posts($args);
}

?>