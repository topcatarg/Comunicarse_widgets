<?php
/**
 * Plugin Name: Comunicarse Opinion Post Type
 * Plugin URI: https://comunicarseweb.com
 * Description: Plugin personalizado para el tipo de publicación Opinion con metadatos específicos para migración desde Drupal.
 * Version: 1.0.0
 * Author: Tu Nombre
 * License: GPL v2 or later
 * Text Domain: comunicarse-opinion
 * Domain Path: /languages
 */

// Prevenir acceso directo
if (!defined('ABSPATH')) {
    exit;
}

// Definir constantes del plugin
define('COMUNICARSE_OPINION_VERSION', '1.0.0');
define('COMUNICARSE_OPINION_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('COMUNICARSE_OPINION_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * Clase principal del plugin
 */
class ComunicarseOpinionPlugin {
    
    public function __construct() {
        add_action('init', array($this, 'init'));
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
    }

    /**
     * Inicializar el plugin
     */
    public function init() {
        // Cargar traducciones
        load_plugin_textdomain('comunicarse-opinion', false, dirname(plugin_basename(__FILE__)) . '/languages');
        
        // Registrar post type y taxonomías
        $this->register_post_type();
        $this->register_taxonomies();
        
        // Agregar metaboxes
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
        add_action('save_post', array($this, 'save_metadata'));
        
        // Personalizar columnas del admin
        add_filter('manage_opinion_posts_columns', array($this, 'add_admin_columns'));
        add_action('manage_opinion_posts_custom_column', array($this, 'display_admin_columns'), 10, 2);
        add_filter('manage_edit-opinion_sortable_columns', array($this, 'make_columns_sortable'));
        
        // Agregar estilos admin
        add_action('admin_enqueue_scripts', array($this, 'admin_styles'));
    }

    /**
     * Registrar el Custom Post Type Opinion
     */
    public function register_post_type() {
        $labels = array(
            'name'                  => _x('Opiniones', 'Post type general name', 'comunicarse-opinion'),
            'singular_name'         => _x('Opinión', 'Post type singular name', 'comunicarse-opinion'),
            'menu_name'             => _x('Opiniones', 'Admin Menu text', 'comunicarse-opinion'),
            'name_admin_bar'        => _x('Opinión', 'Add New on Toolbar', 'comunicarse-opinion'),
            'add_new'               => __('Agregar Nueva', 'comunicarse-opinion'),
            'add_new_item'          => __('Agregar Nueva Opinión', 'comunicarse-opinion'),
            'new_item'              => __('Nueva Opinión', 'comunicarse-opinion'),
            'edit_item'             => __('Editar Opinión', 'comunicarse-opinion'),
            'view_item'             => __('Ver Opinión', 'comunicarse-opinion'),
            'all_items'             => __('Todas las Opiniones', 'comunicarse-opinion'),
            'search_items'          => __('Buscar Opiniones', 'comunicarse-opinion'),
            'parent_item_colon'     => __('Opiniones Padre:', 'comunicarse-opinion'),
            'not_found'             => __('No se encontraron opiniones.', 'comunicarse-opinion'),
            'not_found_in_trash'    => __('No se encontraron opiniones en la papelera.', 'comunicarse-opinion'),
            'featured_image'        => _x('Imagen Destacada', 'Overrides the "Featured Image" phrase', 'comunicarse-opinion'),
            'set_featured_image'    => _x('Establecer imagen destacada', 'Overrides the "Set featured image" phrase', 'comunicarse-opinion'),
            'remove_featured_image' => _x('Remover imagen destacada', 'Overrides the "Remove featured image" phrase', 'comunicarse-opinion'),
            'use_featured_image'    => _x('Usar como imagen destacada', 'Overrides the "Use as featured image" phrase', 'comunicarse-opinion'),
            'archives'              => _x('Archivo de Opiniones', 'The post type archive label', 'comunicarse-opinion'),
            'insert_into_item'      => _x('Insertar en opinión', 'Overrides the "Insert into post" phrase', 'comunicarse-opinion'),
            'uploaded_to_this_item' => _x('Subido a esta opinión', 'Overrides the "Uploaded to this post" phrase', 'comunicarse-opinion'),
            'filter_items_list'     => _x('Filtrar lista de opiniones', 'Screen reader text for the filter links', 'comunicarse-opinion'),
            'items_list_navigation' => _x('Navegación de lista de opiniones', 'Screen reader text for the pagination', 'comunicarse-opinion'),
            'items_list'            => _x('Lista de opiniones', 'Screen reader text for the items list', 'comunicarse-opinion'),
        );

        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'show_in_nav_menus'  => true,
            'show_in_admin_bar'  => true,
            'query_var'          => true,
            'rewrite'            => array('slug' => 'opinion', 'with_front' => false),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => 5,
            'menu_icon'          => 'dashicons-format-chat',
            'supports'           => array('title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments', 'custom-fields', 'revisions'),
            'show_in_rest'       => true,
            'rest_base'          => 'opiniones',
            'rest_controller_class' => 'WP_REST_Posts_Controller',
        );

        register_post_type('opinion', $args);
    }

    /**
     * Registrar taxonomías personalizadas
     */
    public function register_taxonomies() {
        // Categorías de Opinión
        $labels_categoria = array(
            'name'              => _x('Categorías de Opinión', 'taxonomy general name', 'comunicarse-opinion'),
            'singular_name'     => _x('Categoría de Opinión', 'taxonomy singular name', 'comunicarse-opinion'),
            'search_items'      => __('Buscar Categorías', 'comunicarse-opinion'),
            'all_items'         => __('Todas las Categorías', 'comunicarse-opinion'),
            'parent_item'       => __('Categoría Padre', 'comunicarse-opinion'),
            'parent_item_colon' => __('Categoría Padre:', 'comunicarse-opinion'),
            'edit_item'         => __('Editar Categoría', 'comunicarse-opinion'),
            'update_item'       => __('Actualizar Categoría', 'comunicarse-opinion'),
            'add_new_item'      => __('Agregar Nueva Categoría', 'comunicarse-opinion'),
            'new_item_name'     => __('Nuevo Nombre de Categoría', 'comunicarse-opinion'),
            'menu_name'         => __('Categorías', 'comunicarse-opinion'),
        );

        register_taxonomy('categoria_opinion', array('opinion'), array(
            'hierarchical'      => true,
            'labels'            => $labels_categoria,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array('slug' => 'categoria-opinion'),
            'show_in_rest'      => true,
        ));

        // Tags de Opinión
        $labels_tags = array(
            'name'                       => _x('Tags de Opinión', 'taxonomy general name', 'comunicarse-opinion'),
            'singular_name'              => _x('Tag de Opinión', 'taxonomy singular name', 'comunicarse-opinion'),
            'search_items'               => __('Buscar Tags', 'comunicarse-opinion'),
            'popular_items'              => __('Tags Populares', 'comunicarse-opinion'),
            'all_items'                  => __('Todos los Tags', 'comunicarse-opinion'),
            'edit_item'                  => __('Editar Tag', 'comunicarse-opinion'),
            'update_item'                => __('Actualizar Tag', 'comunicarse-opinion'),
            'add_new_item'               => __('Agregar Nuevo Tag', 'comunicarse-opinion'),
            'new_item_name'              => __('Nuevo Nombre de Tag', 'comunicarse-opinion'),
            'separate_items_with_commas' => __('Separar tags con comas', 'comunicarse-opinion'),
            'add_or_remove_items'        => __('Agregar o remover tags', 'comunicarse-opinion'),
            'choose_from_most_used'      => __('Elegir de los más usados', 'comunicarse-opinion'),
            'not_found'                  => __('No se encontraron tags.', 'comunicarse-opinion'),
            'menu_name'                  => __('Tags', 'comunicarse-opinion'),
        );

        register_taxonomy('tag_opinion', 'opinion', array(
            'hierarchical'          => false,
            'labels'                => $labels_tags,
            'show_ui'               => true,
            'show_admin_column'     => true,
            'update_count_callback' => '_update_post_term_count',
            'query_var'             => true,
            'rewrite'               => array('slug' => 'tag-opinion'),
            'show_in_rest'          => true,
        ));
    }

    /**
     * Agregar metaboxes
     */
    public function add_meta_boxes() {
        add_meta_box(
            'opinion_metadatos',
            __('Metadatos de Opinión', 'comunicarse-opinion'),
            array($this, 'display_meta_box'),
            'opinion',
            'normal',
            'high'
        );
    }

    /**
     * Mostrar el metabox
     */
    public function display_meta_box($post) {
        wp_nonce_field('guardar_metadatos_opinion', 'opinion_nonce');

        // Obtener metadatos específicos que queremos
        $opinion_quote = get_post_meta($post->ID, '_mh_opinion_quote', true);
        $custom_author_name = get_post_meta($post->ID, '_mh_custom_author_name', true);
        $author_title = get_post_meta($post->ID, '_mh_author_title', true);
        $author_photo_fid = get_post_meta($post->ID, '_mh_author_photo_fid', true);
        
        ?>
        <div class="opinion-metabox">
            <table class="form-table">
                <tr>
                    <th><label for="mh_opinion_quote"><?php _e('Frase de Opinión:', 'comunicarse-opinion'); ?></label></th>
                    <td>
                        <textarea id="mh_opinion_quote" name="mh_opinion_quote" rows="4" class="large-text" placeholder="<?php _e('Ingrese la frase destacada de la opinión...', 'comunicarse-opinion'); ?>"><?php echo esc_textarea($opinion_quote); ?></textarea>
                        <p class="description"><?php _e('Frase principal que representa la opinión del autor.', 'comunicarse-opinion'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th><label for="mh_custom_author_name"><?php _e('Nombre del Autor:', 'comunicarse-opinion'); ?></label></th>
                    <td>
                        <input type="text" id="mh_custom_author_name" name="mh_custom_author_name" value="<?php echo esc_attr($custom_author_name); ?>" class="regular-text" placeholder="<?php _e('Gonzalo Bianchi', 'comunicarse-opinion'); ?>" />
                        <p class="description"><?php _e('Nombre completo del autor de la opinión.', 'comunicarse-opinion'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th><label for="mh_author_title"><?php _e('Cargo del Autor:', 'comunicarse-opinion'); ?></label></th>
                    <td>
                        <input type="text" id="mh_author_title" name="mh_author_title" value="<?php echo esc_attr($author_title); ?>" class="regular-text" placeholder="<?php _e('Ej: Director, Analista, Columnista', 'comunicarse-opinion'); ?>" />
                        <p class="description"><?php _e('Título o cargo profesional del autor.', 'comunicarse-opinion'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th><label for="mh_author_photo_fid"><?php _e('Foto del Autor:', 'comunicarse-opinion'); ?></label></th>
                    <td>
                        <div class="author-photo-upload">
                            <input type="hidden" id="mh_author_photo_fid" name="mh_author_photo_fid" value="<?php echo esc_attr($author_photo_fid); ?>" />
                            <button type="button" class="button upload-author-photo" id="upload_author_photo_btn">
                                <?php _e('Seleccionar Foto', 'comunicarse-opinion'); ?>
                            </button>
                            <button type="button" class="button remove-author-photo" id="remove_author_photo_btn" style="<?php echo !$author_photo_fid ? 'display:none;' : ''; ?>">
                                <?php _e('Remover Foto', 'comunicarse-opinion'); ?>
                            </button>
                            <div class="author-photo-preview" id="author_photo_preview">
                                <?php if ($author_photo_fid): 
                                    $image = wp_get_attachment_image($author_photo_fid, 'thumbnail', false, array('style' => 'max-width: 150px; height: auto; margin-top: 10px;'));
                                    echo $image;
                                endif; ?>
                            </div>
                            <p class="description"><?php _e('Foto del autor para mostrar junto con la opinión. Tamaño recomendado: 150x150px.', 'comunicarse-opinion'); ?></p>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
        
        <script type="text/javascript">
        jQuery(document).ready(function($) {
            var frame;
            
            // Abrir media uploader
            $('#upload_author_photo_btn').on('click', function(e) {
                e.preventDefault();
                
                if (frame) {
                    frame.open();
                    return;
                }
                
                frame = wp.media({
                    title: '<?php _e('Seleccionar Foto del Autor', 'comunicarse-opinion'); ?>',
                    button: {
                        text: '<?php _e('Usar esta imagen', 'comunicarse-opinion'); ?>'
                    },
                    library: {
                        type: 'image'
                    },
                    multiple: false
                });
                
                frame.on('select', function() {
                    var attachment = frame.state().get('selection').first().toJSON();
                    $('#mh_author_photo_fid').val(attachment.id);
                    $('#author_photo_preview').html('<img src="' + attachment.sizes.thumbnail.url + '" style="max-width: 150px; height: auto; margin-top: 10px;" />');
                    $('#remove_author_photo_btn').show();
                });
                
                frame.open();
            });
            
            // Remover foto
            $('#remove_author_photo_btn').on('click', function(e) {
                e.preventDefault();
                $('#mh_author_photo_fid').val('');
                $('#author_photo_preview').html('');
                $(this).hide();
            });
        });
        </script>
        <?php
    }

    /**
     * Guardar metadatos
     */
    public function save_metadata($post_id) {
        if (!isset($_POST['opinion_nonce']) || !wp_verify_nonce($_POST['opinion_nonce'], 'guardar_metadatos_opinion')) {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (get_post_type($post_id) !== 'opinion') {
            return;
        }

        // Campos específicos de MH Magazine para opiniones
        $campos = array(
            'mh_opinion_quote' => 'sanitize_textarea_field',
            'mh_custom_author_name' => 'sanitize_text_field',
            'mh_author_title' => 'sanitize_text_field',
            'mh_author_photo_fid' => 'absint'
        );

        foreach ($campos as $campo => $sanitize_callback) {
            if (isset($_POST[$campo])) {
                $value = call_user_func($sanitize_callback, $_POST[$campo]);
                update_post_meta($post_id, '_' . $campo, $value);
            }
        }
    }

    /**
     * Agregar columnas personalizadas en el admin
     */
    public function add_admin_columns($columns) {
        $new_columns = array();
        foreach ($columns as $key => $value) {
            $new_columns[$key] = $value;
            if ($key === 'title') {
                $new_columns['custom_author'] = __('Autor', 'comunicarse-opinion');
                $new_columns['author_title'] = __('Cargo', 'comunicarse-opinion');
                $new_columns['opinion_quote'] = __('Frase', 'comunicarse-opinion');
            }
        }
        return $new_columns;
    }

    /**
     * Mostrar contenido de las columnas personalizadas
     */
    public function display_admin_columns($column, $post_id) {
        switch ($column) {
            case 'custom_author':
                $author_name = get_post_meta($post_id, '_mh_custom_author_name', true);
                $photo_id = get_post_meta($post_id, '_mh_author_photo_fid', true);
                
                if ($photo_id) {
                    $photo = wp_get_attachment_image($photo_id, array(32, 32), false, array('style' => 'border-radius: 50%; margin-right: 8px; vertical-align: middle;'));
                    echo $photo;
                }
                echo esc_html($author_name);
                break;
                
            case 'author_title':
                echo esc_html(get_post_meta($post_id, '_mh_author_title', true));
                break;
                
            case 'opinion_quote':
                $quote = get_post_meta($post_id, '_mh_opinion_quote', true);
                if ($quote) {
                    $short_quote = wp_trim_words($quote, 8, '...');
                    echo '<em>"' . esc_html($short_quote) . '"</em>';
                }
                break;
        }
    }

    /**
     * Hacer las columnas ordenables
     */
    public function make_columns_sortable($columns) {
        $columns['custom_author'] = 'mh_custom_author_name';
        $columns['author_title'] = 'mh_author_title';
        return $columns;
    }

    /**
     * Cargar estilos del admin
     */
    public function admin_styles($hook) {
        global $post_type;
        if ($post_type === 'opinion') {
            // Cargar media uploader
            wp_enqueue_media();
            ?>
            <style>
                .opinion-metabox .form-table th {
                    width: 200px;
                    vertical-align: top;
                    padding-top: 15px;
                }
                .opinion-metabox .form-table td {
                    padding-bottom: 20px;
                }
                .author-photo-upload {
                    position: relative;
                }
                .author-photo-preview img {
                    border: 2px solid #ddd;
                    border-radius: 8px;
                    display: block;
                }
                .remove-author-photo {
                    margin-left: 10px;
                    color: #d63638;
                }
                .opinion-metabox textarea {
                    font-family: Georgia, serif;
                    font-style: italic;
                }
                .opinion-metabox .description {
                    font-style: italic;
                    color: #666;
                    margin-top: 5px;
                }
            </style>
            <?php
        }
    }

    /**
     * Activar plugin
     */
    public function activate() {
        $this->register_post_type();
        $this->register_taxonomies();
        flush_rewrite_rules();
    }

    /**
     * Desactivar plugin
     */
    public function deactivate() {
        flush_rewrite_rules();
    }
}

// Inicializar el plugin
new ComunicarseOpinionPlugin();

/**
 * Funciones de utilidad para usar en el tema
 */

// Obtener metadatos de una opinión
function get_opinion_metadata($post_id = null) {
    if (!$post_id) {
        global $post;
        $post_id = $post->ID;
    }

    return array(
        'quote' => get_post_meta($post_id, '_mh_opinion_quote', true),
        'author_name' => get_post_meta($post_id, '_mh_custom_author_name', true),
        'author_title' => get_post_meta($post_id, '_mh_author_title', true),
        'author_photo_id' => get_post_meta($post_id, '_mh_author_photo_fid', true)
    );
}

// Obtener foto del autor con tamaño específico
function get_opinion_author_photo($post_id = null, $size = 'thumbnail') {
    if (!$post_id) {
        global $post;
        $post_id = $post->ID;
    }
    
    $photo_id = get_post_meta($post_id, '_mh_author_photo_fid', true);
    if ($photo_id) {
        return wp_get_attachment_image($photo_id, $size);
    }
    return false;
}

// Mostrar información completa del autor de opinión
function display_opinion_author_info($post_id = null, $show_photo = true) {
    $metadata = get_opinion_metadata($post_id);
    
    if (!$metadata['author_name']) {
        return false;
    }
    
    $output = '<div class="opinion-author-info">';
    
    if ($show_photo && $metadata['author_photo_id']) {
        $photo = wp_get_attachment_image($metadata['author_photo_id'], 'thumbnail', false, array(
            'class' => 'opinion-author-photo',
            'style' => 'border-radius: 50%; width: 80px; height: 80px; object-fit: cover;'
        ));
        $output .= '<div class="author-photo">' . $photo . '</div>';
    }
    
    $output .= '<div class="author-details">';
    $output .= '<h4 class="author-name">' . esc_html($metadata['author_name']) . '</h4>';
    
    if ($metadata['author_title']) {
        $output .= '<p class="author-title">' . esc_html($metadata['author_title']) . '</p>';
    }
    
    $output .= '</div>';
    $output .= '</div>';
    
    return $output;
}
?>