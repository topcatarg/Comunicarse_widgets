<?php
/**
 * Plugin Name: Publicaciones Investigaciones
 * Description: Plugin para mostrar publicaciones de Investigaciones con archivos adjuntos
 * Version: 1.0
 * Author: Gonzalo Bianchi
 * Text Domain: publicaciones-investigaciones
 * Domain Path: /languages
 */

// Prevenir acceso directo
if (!defined('ABSPATH')) {
    exit;
}

// Definir constantes del plugin
define('PUBLICACIONES_INVESTIGACIONES_VERSION', '1.0');
define('PUBLICACIONES_INVESTIGACIONES_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('PUBLICACIONES_INVESTIGACIONES_PLUGIN_URL', plugin_dir_url(__FILE__));

class PublicacionesInvestigacionesPlugin {
    
    public function __construct() {
        add_action('init', array($this, 'init'));
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
        add_action('save_post', array($this, 'save_metadata'));
        
        // Hooks para manejar archivos adjuntos
        add_action('wp_ajax_upload_investigacion_file', array($this, 'upload_file_ajax'));
        add_action('wp_ajax_remove_investigacion_file', array($this, 'remove_file_ajax'));
        
        // Enqueue scripts y estilos
        add_action('admin_enqueue_scripts', array($this, 'admin_scripts_and_styles'));
        
        // Hook para interceptar templates de taxonomías
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
        
        // No necesitamos traducciones - todo en español
    }
    
    /**
     * Registrar el custom post type 'investigaciones'
     */
    public function register_post_type() {
        $labels = array(
            'name'                  => 'Investigaciones',
            'singular_name'         => 'Investigación',
            'menu_name'             => 'Investigaciones',
            'name_admin_bar'        => 'Investigación',
            'archives'              => 'Archivo de Investigaciones',
            'attributes'            => 'Atributos de Investigación',
            'parent_item_colon'     => 'Investigación Padre:',
            'all_items'             => 'Todas las Investigaciones',
            'add_new_item'          => 'Agregar Nueva Investigación',
            'add_new'               => 'Agregar Nueva',
            'new_item'              => 'Nueva Investigación',
            'edit_item'             => 'Editar Investigación',
            'update_item'           => 'Actualizar Investigación',
            'view_item'             => 'Ver Investigación',
            'view_items'            => 'Ver Investigaciones',
            'search_items'          => 'Buscar Investigaciones',
            'not_found'             => 'No encontrado',
            'not_found_in_trash'    => 'No encontrado en papelera',
            'featured_image'        => 'Imagen Destacada',
            'set_featured_image'    => 'Establecer imagen destacada',
            'remove_featured_image' => 'Remover imagen destacada',
            'use_featured_image'    => 'Usar como imagen destacada',
            'insert_into_item'      => 'Insertar en investigación',
            'uploaded_to_this_item' => 'Subido a esta investigación',
            'items_list'            => 'Lista de investigaciones',
            'items_list_navigation' => 'Navegación de lista de investigaciones',
            'filter_items_list'     => 'Filtrar lista de investigaciones',
        );
        
        $args = array(
            'label'                 => __('Investigación', 'publicaciones-investigaciones'),
            'description'           => 'Publicaciones de Investigaciones con archivos adjuntos',
            'labels'                => $labels,
            'supports'              => array('title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'revisions', 'custom-fields'),
            'taxonomies'            => array('category_investigacion', 'tag_investigacion'),
            'hierarchical'          => false,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'menu_position'         => 6,
            'menu_icon'             => 'dashicons-search',
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => true,
            'can_export'            => true,
            'has_archive'           => 'investigaciones',
            'exclude_from_search'   => false,
            'publicly_queryable'    => true,
            'rewrite'               => array(
                'slug' => 'investigacion',
                'with_front' => false,
                'pages' => true,
                'feeds' => true,
            ),
            'capability_type'       => 'post',
            'show_in_rest'          => true,
            'rest_base'             => 'investigaciones',
            'rest_controller_class' => 'WP_REST_Posts_Controller',
        );
        
        register_post_type('investigaciones', $args);
    }
    
    /**
     * Registrar taxonomías personalizadas para investigaciones
     */
    public function register_taxonomies() {
        // Registrar taxonomía de categorías para investigaciones
        $this->register_category_investigacion_taxonomy();
        
        // Registrar taxonomía de tags para investigaciones
        $this->register_tag_investigacion_taxonomy();
    }
    
    /**
     * Registrar taxonomía category_investigacion
     */
    private function register_category_investigacion_taxonomy() {
        $labels = array(
            'name'                       => 'Categorías Investigación',
            'singular_name'              => 'Categoría Investigación',
            'menu_name'                  => 'Categorías Investigación',
            'all_items'                  => 'Todas las Categorías',
            'parent_item'                => 'Categoría Padre',
            'parent_item_colon'          => 'Categoría Padre:',
            'new_item_name'              => 'Nombre de Nueva Categoría',
            'add_new_item'               => 'Agregar Nueva Categoría',
            'edit_item'                  => 'Editar Categoría',
            'update_item'                => 'Actualizar Categoría',
            'view_item'                  => 'Ver Categoría',
            'separate_items_with_commas' => 'Separar categorías con comas',
            'add_or_remove_items'        => 'Agregar o remover categorías',
            'choose_from_most_used'      => 'Elegir de las más usadas',
            'popular_items'              => 'Categorías Populares',
            'search_items'               => 'Buscar Categorías',
            'not_found'                  => 'No Encontrado',
            'no_terms'                   => 'No hay categorías',
            'items_list'                 => 'Lista de categorías',
            'items_list_navigation'      => 'Navegación de lista de categorías',
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
            'rewrite'                    => array(
                'slug' => 'investigacion-categoria',
                'with_front' => false,
                'hierarchical' => true,
            ),
        );
        
        register_taxonomy('category_investigacion', array('investigaciones'), $args);
    }
    
    /**
     * Registrar taxonomía tag_investigacion
     */
    private function register_tag_investigacion_taxonomy() {
        $labels = array(
            'name'                       => 'Tags Investigación',
            'singular_name'              => 'Tag Investigación',
            'menu_name'                  => 'Tags Investigación',
            'all_items'                  => 'Todos los Tags',
            'parent_item'                => 'Tag Padre',
            'parent_item_colon'          => 'Tag Padre:',
            'new_item_name'              => 'Nombre de Nuevo Tag',
            'add_new_item'               => 'Agregar Nuevo Tag',
            'edit_item'                  => 'Editar Tag',
            'update_item'                => 'Actualizar Tag',
            'view_item'                  => 'Ver Tag',
            'separate_items_with_commas' => 'Separar tags con comas',
            'add_or_remove_items'        => 'Agregar o remover tags',
            'choose_from_most_used'      => 'Elegir de los más usados',
            'popular_items'              => 'Tags Populares',
            'search_items'               => 'Buscar Tags',
            'not_found'                  => 'No Encontrado',
            'no_terms'                   => 'No hay tags',
            'items_list'                 => 'Lista de tags',
            'items_list_navigation'      => 'Navegación de lista de tags',
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
            'rewrite'                    => array(
                'slug' => 'investigacion-tag',
                'with_front' => false,
            ),
        );
        
        register_taxonomy('tag_investigacion', array('investigaciones'), $args);
    }
    
    /**
     * Agregar metaboxes personalizados
     */
    public function add_meta_boxes() {
        add_meta_box(
            'investigacion_archivos_meta_box',
            'Archivos Adjuntos',
            array($this, 'render_archivos_meta_box'),
            'investigaciones',
            'normal',
            'high'
        );
    }
    
    /**
     * Renderizar metabox de archivos adjuntos
     */
    public function render_archivos_meta_box($post) {
        wp_nonce_field('investigacion_archivos_nonce_action', 'investigacion_archivos_nonce');
        
        $archivos_adjuntos = get_post_meta($post->ID, '_investigacion_archivos_adjuntos', true);
        if (!is_array($archivos_adjuntos)) {
            $archivos_adjuntos = array();
        }
        
        ?>
        <div id="investigacion_archivos_container">
            <div class="archivos-header">
                <p><strong>Archivos adjuntos de la investigación</strong></p>
                <p class="description">Puedes subir múltiples archivos (PDF, DOC, DOCX, XLS, XLSX, etc.)</p>
            </div>
            
            <div class="upload-section">
                <button type="button" id="add_archivo_btn" class="button button-secondary">
                    <span class="dashicons dashicons-plus-alt"></span> Agregar Archivo
                </button>
            </div>
            
            <div id="archivos_list" class="archivos-list">
                <?php foreach ($archivos_adjuntos as $index => $archivo) : ?>
                    <div class="archivo-item" data-index="<?php echo $index; ?>">
                        <div class="archivo-info">
                            <div class="archivo-thumbnail">
                                <?php 
                                $extension = pathinfo($archivo['nombre'], PATHINFO_EXTENSION);
                                $icon_class = $this->get_file_icon_class($extension);
                                ?>
                                <span class="dashicons <?php echo $icon_class; ?>"></span>
                            </div>
                            <div class="archivo-details">
                                <input type="text" name="investigacion_archivos[<?php echo $index; ?>][titulo]" 
                                       placeholder="Título del archivo" 
                                       value="<?php echo esc_attr($archivo['titulo'] ?? ''); ?>" 
                                       class="widefat archivo-titulo" />
                                <input type="hidden" name="investigacion_archivos[<?php echo $index; ?>][url]" 
                                       value="<?php echo esc_attr($archivo['url']); ?>" />
                                <input type="hidden" name="investigacion_archivos[<?php echo $index; ?>][nombre]" 
                                       value="<?php echo esc_attr($archivo['nombre']); ?>" />
                                <input type="hidden" name="investigacion_archivos[<?php echo $index; ?>][size]" 
                                       value="<?php echo esc_attr($archivo['size'] ?? 0); ?>" />
                                <div class="archivo-meta">
                                    <span class="archivo-name"><?php echo esc_html($archivo['nombre']); ?></span>
                                    <span class="archivo-size">(<?php echo $this->format_file_size($archivo['size'] ?? 0); ?>)</span>
                                </div>
                                <div class="archivo-description">
                                    <textarea name="investigacion_archivos[<?php echo $index; ?>][descripcion]" 
                                              placeholder="Descripción del archivo (opcional)"
                                              rows="2" class="widefat"><?php echo esc_textarea($archivo['descripcion'] ?? ''); ?></textarea>
                                </div>
                            </div>
                            <div class="archivo-actions">
                                <a href="<?php echo esc_url($archivo['url']); ?>" target="_blank" 
                                   class="button button-small" title="Ver archivo">
                                    <span class="dashicons dashicons-visibility"></span>
                                </a>
                                <button type="button" class="button button-small remove-archivo" 
                                        title="Eliminar archivo">
                                    <span class="dashicons dashicons-trash"></span>
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
    }
    
    /**
     * Obtener clase de icono según extensión de archivo
     */
    private function get_file_icon_class($extension) {
        $extension = strtolower($extension);
        
        $icon_map = array(
            'pdf' => 'dashicons-media-document',
            'doc' => 'dashicons-media-text',
            'docx' => 'dashicons-media-text',
            'xls' => 'dashicons-media-spreadsheet',
            'xlsx' => 'dashicons-media-spreadsheet',
            'ppt' => 'dashicons-media-interactive',
            'pptx' => 'dashicons-media-interactive',
            'zip' => 'dashicons-media-archive',
            'rar' => 'dashicons-media-archive',
            'txt' => 'dashicons-media-text',
            'csv' => 'dashicons-media-spreadsheet',
        );
        
        return isset($icon_map[$extension]) ? $icon_map[$extension] : 'dashicons-media-default';
    }
    
    /**
     * Formatear tamaño de archivo
     */
    private function format_file_size($bytes) {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }
    
    /**
     * Guardar metadatos
     */
    public function save_metadata($post_id) {
        if (!isset($_POST['investigacion_archivos_nonce']) || 
            !wp_verify_nonce($_POST['investigacion_archivos_nonce'], 'investigacion_archivos_nonce_action')) {
            return;
        }
        
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        
        if (get_post_type($post_id) !== 'investigaciones') {
            return;
        }
        
        // Guardar archivos adjuntos
        if (isset($_POST['investigacion_archivos']) && is_array($_POST['investigacion_archivos'])) {
            $archivos_adjuntos = array();
            
            foreach ($_POST['investigacion_archivos'] as $archivo) {
                if (!empty($archivo['url'])) {
                    $archivos_adjuntos[] = array(
                        'titulo' => sanitize_text_field($archivo['titulo']),
                        'descripcion' => sanitize_textarea_field($archivo['descripcion']),
                        'url' => esc_url_raw($archivo['url']),
                        'nombre' => sanitize_file_name($archivo['nombre']),
                        'size' => intval($archivo['size'])
                    );
                }
            }
            
            update_post_meta($post_id, '_investigacion_archivos_adjuntos', $archivos_adjuntos);
        } else {
            delete_post_meta($post_id, '_investigacion_archivos_adjuntos');
        }
    }
    
    /**
     * Enqueue scripts y estilos del admin
     */
    public function admin_scripts_and_styles($hook) {
        global $post_type;
        
        if ($post_type !== 'investigaciones') {
            return;
        }
        
        if ($hook === 'post-new.php' || $hook === 'post.php') {
            wp_enqueue_media();
            wp_enqueue_script(
                'investigaciones-admin',
                PUBLICACIONES_INVESTIGACIONES_PLUGIN_URL . 'js/admin.js',
                array('jquery', 'media-upload'),
                PUBLICACIONES_INVESTIGACIONES_VERSION,
                true
            );
            
            wp_localize_script('investigaciones-admin', 'investigacionesAjax', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('investigaciones_ajax_nonce'),
                'strings' => array(
                    'select_files' => 'Seleccionar Archivos',
                    'use_files' => 'Usar estos archivos',
                    'confirm_remove' => '¿Estás seguro de que quieres eliminar este archivo?',
                )
            ));
            
            wp_enqueue_style(
                'investigaciones-admin',
                PUBLICACIONES_INVESTIGACIONES_PLUGIN_URL . 'css/admin.css',
                array(),
                PUBLICACIONES_INVESTIGACIONES_VERSION
            );
        }
    }
    
    /**
     * AJAX handler para subir archivos
     */
    public function upload_file_ajax() {
        check_ajax_referer('investigaciones_ajax_nonce', 'nonce');
        
        if (!current_user_can('upload_files')) {
            wp_die('No tienes permisos para subir archivos.');
        }
        
        if (!function_exists('wp_handle_upload')) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
        }
        
        $response = array();
        
        if (!empty($_FILES['files'])) {
            $files = $_FILES['files'];
            $uploaded_files = array();
            
            for ($i = 0; $i < count($files['name']); $i++) {
                if ($files['error'][$i] === UPLOAD_ERR_OK) {
                    $file = array(
                        'name' => $files['name'][$i],
                        'type' => $files['type'][$i],
                        'tmp_name' => $files['tmp_name'][$i],
                        'error' => $files['error'][$i],
                        'size' => $files['size'][$i]
                    );
                    
                    $uploaded_file = wp_handle_upload($file, array('test_form' => false));
                    
                    if (isset($uploaded_file['file'])) {
                        $uploaded_files[] = array(
                            'url' => $uploaded_file['url'],
                            'nombre' => basename($uploaded_file['file']),
                            'size' => $files['size'][$i],
                            'titulo' => pathinfo($files['name'][$i], PATHINFO_FILENAME)
                        );
                    }
                }
            }
            
            $response['success'] = true;
            $response['files'] = $uploaded_files;
        } else {
            $response['success'] = false;
            $response['message'] = 'No se recibieron archivos.';
        }
        
        wp_send_json($response);
    }
    
    /**
     * AJAX handler para remover archivos
     */
    public function remove_file_ajax() {
        check_ajax_referer('investigaciones_ajax_nonce', 'nonce');
        
        $file_url = sanitize_url($_POST['file_url']);
        $post_id = intval($_POST['post_id']);
        
        if (!current_user_can('edit_post', $post_id)) {
            wp_die('No tienes permisos para editar esta investigación.');
        }
        
        // Aquí podrías implementar la lógica para eliminar el archivo físico si lo deseas
        // Por ahora solo respondemos con éxito
        
        wp_send_json_success(array(
            'message' => 'Archivo eliminado correctamente.'
        ));
    }
    
    /**
     * Interceptar templates de taxonomías
     */
    public function custom_taxonomy_template($template) {
        if (is_tax('category_investigacion') || is_tax('tag_investigacion')) {
            
            if (is_tax('category_investigacion')) {
                $term = get_queried_object();
                
                $GLOBALS['investigacion_category'] = $term;
                $GLOBALS['is_investigacion_category_archive'] = true;
                
                $templates = array(
                    "archive-investigaciones-category-{$term->slug}.php",
                    "archive-investigaciones-category.php",
                    "taxonomy-category_investigacion-{$term->slug}.php",
                    "taxonomy-category_investigacion.php",
                    "archive-investigaciones.php",
                    "taxonomy.php",
                    "archive.php",
                    "index.php"
                );
                
                $custom_template = locate_template($templates);
                
                if ($custom_template) {
                    return $custom_template;
                }
            }
            
            if (is_tax('tag_investigacion')) {
                $term = get_queried_object();
                
                $GLOBALS['investigacion_tag'] = $term;
                $GLOBALS['is_investigacion_tag_archive'] = true;
                
                $templates = array(
                    "archive-investigaciones-tag-{$term->slug}.php",
                    "archive-investigaciones-tag.php",
                    "taxonomy-tag_investigacion-{$term->slug}.php",
                    "taxonomy-tag_investigacion.php",
                    "archive-investigaciones.php",
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
new PublicacionesInvestigacionesPlugin();

/**
 * FUNCIONES HELPER PARA USAR EN TEMPLATES
 */

/**
 * Función para obtener investigaciones
 */
function get_investigaciones($args = array()) {
    $default_args = array(
        'post_type' => 'investigaciones',
        'post_status' => 'publish'
    );
    
    $args = wp_parse_args($args, $default_args);
    return get_posts($args);
}

/**
 * Función para obtener categorías de investigación
 */
function get_investigacion_categories($args = array()) {
    $default_args = array(
        'taxonomy' => 'category_investigacion',
        'hide_empty' => false
    );
    
    $args = wp_parse_args($args, $default_args);
    return get_terms($args);
}

/**
 * Función para obtener tags de investigación
 */
function get_investigacion_tags($args = array()) {
    $default_args = array(
        'taxonomy' => 'tag_investigacion',
        'hide_empty' => false
    );
    
    $args = wp_parse_args($args, $default_args);
    return get_terms($args);
}

/**
 * Función para obtener archivos adjuntos de una investigación
 */
function get_investigacion_archivos_adjuntos($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    $archivos = get_post_meta($post_id, '_investigacion_archivos_adjuntos', true);
    return is_array($archivos) ? $archivos : array();
}

/**
 * Función para mostrar archivos adjuntos de una investigación
 */
function the_investigacion_archivos_adjuntos($post_id = null, $wrapper_class = 'investigacion-archivos') {
    $archivos = get_investigacion_archivos_adjuntos($post_id);
    
    if (!empty($archivos)) {
        echo '<div class="' . esc_attr($wrapper_class) . '">';
        echo '<h4>Archivos Adjuntos</h4>';
        echo '<ul class="archivos-list">';
        
        foreach ($archivos as $archivo) {
            $extension = pathinfo($archivo['nombre'], PATHINFO_EXTENSION);
            $icon_class = get_file_icon_class($extension);
            
            echo '<li class="archivo-item">';
            echo '<span class="archivo-icon dashicons ' . esc_attr($icon_class) . '"></span>';
            echo '<div class="archivo-info">';
            
            if (!empty($archivo['titulo'])) {
                echo '<h5 class="archivo-titulo">' . esc_html($archivo['titulo']) . '</h5>';
            }
            
            echo '<a href="' . esc_url($archivo['url']) . '" target="_blank" class="archivo-link">';
            echo esc_html($archivo['nombre']);
            echo '</a>';
            
            if (!empty($archivo['size'])) {
                echo ' <span class="archivo-size">(' . format_file_size($archivo['size']) . ')</span>';
            }
            
            if (!empty($archivo['descripcion'])) {
                echo '<p class="archivo-descripcion">' . esc_html($archivo['descripcion']) . '</p>';
            }
            
            echo '</div>';
            echo '</li>';
        }
        
        echo '</ul>';
        echo '</div>';
    }
}

/**
 * Función para verificar si un post es una investigación
 */
function is_investigacion_post($post = null) {
    if (!$post) {
        $post = get_post();
    }
    
    if (!$post) {
        return false;
    }
    
    return $post->post_type === 'investigaciones';
}

/**
 * Función para obtener investigaciones de una categoría específica
 */
function get_investigaciones_by_category($category_slug, $args = array()) {
    $default_args = array(
        'post_type' => 'investigaciones',
        'post_status' => 'publish',
        'posts_per_page' => 10,
        'tax_query' => array(
            array(
                'taxonomy' => 'category_investigacion',
                'field' => 'slug',
                'terms' => $category_slug
            )
        )
    );
    
    $args = wp_parse_args($args, $default_args);
    return get_posts($args);
}

/**
 * Función para obtener investigaciones por tag
 */
function get_investigaciones_by_tag($tag_slug, $args = array()) {
    $default_args = array(
        'post_type' => 'investigaciones',
        'post_status' => 'publish',
        'tax_query' => array(
            array(
                'taxonomy' => 'tag_investigacion',
                'field' => 'slug',
                'terms' => $tag_slug
            )
        )
    );
    
    $args = wp_parse_args($args, $default_args);
    return get_posts($args);
}

/**
 * Función para obtener las categorías de una investigación específica
 */
function get_investigacion_post_categories($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    return get_the_terms($post_id, 'category_investigacion');
}

/**
 * Función para obtener los tags de una investigación específica
 */
function get_investigacion_post_tags($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    return get_the_terms($post_id, 'tag_investigacion');
}

/**
 * Función para mostrar las categorías de una investigación
 */
function the_investigacion_categories($separator = ', ', $post_id = null) {
    $categories = get_investigacion_post_categories($post_id);
    
    if (!$categories || is_wp_error($categories)) {
        return;
    }
    
    $category_links = array();
    foreach ($categories as $category) {
        $category_links[] = '<a href="' . get_investigacion_category_archive_url($category->slug) . '">' . esc_html($category->name) . '</a>';
    }
    
    echo implode($separator, $category_links);
}

/**
 * Función para mostrar los tags de una investigación
 */
function the_investigacion_tags($separator = ', ', $post_id = null) {
    $tags = get_investigacion_post_tags($post_id);
    
    if (!$tags || is_wp_error($tags)) {
        return;
    }
    
    $tag_links = array();
    foreach ($tags as $tag) {
        $tag_links[] = '<a href="' . get_investigacion_tag_archive_url($tag->slug) . '">' . esc_html($tag->name) . '</a>';
    }
    
    echo implode($separator, $tag_links);
}

/**
 * Función para obtener URL del archivo de investigaciones
 */
function get_investigaciones_archive_url() {
    return home_url('/investigaciones/');
}

/**
 * Función para obtener URL de archivo de categoría de investigación
 */
function get_investigacion_category_archive_url($category_slug) {
    return home_url('/investigacion-categoria/' . $category_slug . '/');
}

/**
 * Función para obtener URL de archivo de tag de investigación
 */
function get_investigacion_tag_archive_url($tag_slug) {
    return home_url('/investigacion-tag/' . $tag_slug . '/');
}

/**
 * Función para obtener investigaciones relacionadas por categoría
 */
function get_related_investigaciones($post_id = null, $limit = 5) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    $categories = get_investigacion_post_categories($post_id);
    
    if (!$categories || is_wp_error($categories)) {
        return array();
    }
    
    $category_ids = wp_list_pluck($categories, 'term_id');
    
    $args = array(
        'post_type' => 'investigaciones',
        'post_status' => 'publish',
        'posts_per_page' => $limit,
        'post__not_in' => array($post_id),
        'tax_query' => array(
            array(
                'taxonomy' => 'category_investigacion',
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
 * Helper para obtener clase de icono según extensión (reutilizable)
 */
function get_file_icon_class($extension) {
    $extension = strtolower($extension);
    
    $icon_map = array(
        'pdf' => 'dashicons-media-document',
        'doc' => 'dashicons-media-text',
        'docx' => 'dashicons-media-text',
        'xls' => 'dashicons-media-spreadsheet',
        'xlsx' => 'dashicons-media-spreadsheet',
        'ppt' => 'dashicons-media-interactive',
        'pptx' => 'dashicons-media-interactive',
        'zip' => 'dashicons-media-archive',
        'rar' => 'dashicons-media-archive',
        'txt' => 'dashicons-media-text',
        'csv' => 'dashicons-media-spreadsheet',
    );
    
    return isset($icon_map[$extension]) ? $icon_map[$extension] : 'dashicons-media-default';
}

/**
 * Helper para formatear tamaño de archivo (reutilizable)
 */
function format_file_size($bytes) {
    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    } else {
        return $bytes . ' bytes';
    }
}

/**
 * Función para obtener el breadcrumb de investigaciones
 */
function get_investigacion_breadcrumb($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    $breadcrumb = array();
    
    // Agregar enlace a home
    $breadcrumb[] = '<a href="' . home_url() . '">Inicio</a>';
    
    // Agregar enlace al archivo de investigaciones
    $breadcrumb[] = '<a href="' . get_investigaciones_archive_url() . '">Investigaciones</a>';
    
    // Si estamos en un post específico, agregar categorías
    if (is_singular('investigaciones')) {
        $categories = get_investigacion_post_categories($post_id);
        if ($categories && !is_wp_error($categories)) {
            $main_category = $categories[0]; // Tomar la primera categoría
            $breadcrumb[] = '<a href="' . get_investigacion_category_archive_url($main_category->slug) . '">' . esc_html($main_category->name) . '</a>';
        }
        
        $breadcrumb[] = '<span class="current">' . get_the_title($post_id) . '</span>';
    }
    
    return implode(' > ', $breadcrumb);
}

/**
 * Función para mostrar el breadcrumb de investigaciones
 */
function the_investigacion_breadcrumb($post_id = null, $wrapper_class = 'investigacion-breadcrumb') {
    $breadcrumb = get_investigacion_breadcrumb($post_id);
    
    if ($breadcrumb) {
        echo '<div class="' . esc_attr($wrapper_class) . '">' . $breadcrumb . '</div>';
    }
}

/**
 * Función para obtener el resumen de archivos de una investigación
 */
function get_investigacion_archivos_summary($post_id = null) {
    $archivos = get_investigacion_archivos_adjuntos($post_id);
    
    if (empty($archivos)) {
        return null;
    }
    
    $total_archivos = count($archivos);
    $total_size = 0;
    $tipos_archivos = array();
    
    foreach ($archivos as $archivo) {
        $total_size += intval($archivo['size'] ?? 0);
        $extension = strtolower(pathinfo($archivo['nombre'], PATHINFO_EXTENSION));
        $tipos_archivos[$extension] = ($tipos_archivos[$extension] ?? 0) + 1;
    }
    
    return array(
        'total_archivos' => $total_archivos,
        'total_size' => $total_size,
        'total_size_formatted' => format_file_size($total_size),
        'tipos_archivos' => $tipos_archivos
    );
}

/**
 * Función para mostrar el resumen de archivos
 */
function the_investigacion_archivos_summary($post_id = null, $wrapper_class = 'investigacion-archivos-summary') {
    $summary = get_investigacion_archivos_summary($post_id);
    
    if (!$summary) {
        return;
    }
    
    echo '<div class="' . esc_attr($wrapper_class) . '">';
    echo '<p><strong>' . sprintf($summary['total_archivos'] > 1 ? '%d archivos adjuntos' : '%d archivo adjunto', $summary['total_archivos']) . '</strong>';
    
    if ($summary['total_size'] > 0) {
        echo ' (' . $summary['total_size_formatted'] . ')';
    }
    
    echo '</p>';
    echo '</div>';
}