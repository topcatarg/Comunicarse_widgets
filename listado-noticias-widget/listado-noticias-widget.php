<?php
/**
 * Plugin Name: Listado de Noticias Widget
 * Description: Widget que replica el formato del listado de noticias de Drupal con imagen destacada y título sobre fondo gris
 * Version: 1.0
 * Author: Tu Nombre
 */

// Evitar acceso directo
if (!defined('ABSPATH')) {
    exit;
}

// Definir constantes del plugin
define('LISTADO_NOTICIAS_PLUGIN_URL', plugin_dir_url(__FILE__));
define('LISTADO_NOTICIAS_PLUGIN_PATH', plugin_dir_path(__FILE__));

/**
 * Clase principal del widget
 */
class Listado_Noticias_Widget extends WP_Widget {

    /**
     * Constructor del widget
     */
    public function __construct() {
        $widget_options = array(
            'classname' => 'listado_noticias_widget',
            'description' => 'Muestra publicaciones con el formato del sitio original (imagen destacada y título sobre fondo gris)'
        );
        
        parent::__construct(
            'listado_noticias_widget', 
            'Listado de Noticias',
            $widget_options
        );
        
        // Encolar estilos y scripts solo cuando se usa el widget
        add_action('wp_enqueue_scripts', array($this, 'enqueue_assets'));
    }

    /**
     * Encolar assets (CSS y JS)
     */
    public function enqueue_assets() {
        // Verificar si el widget está activo en la página actual
        if (is_active_widget(false, false, $this->id_base)) {
            // Encolar CSS
            wp_enqueue_style(
                'listado-noticias-widget-css',
                LISTADO_NOTICIAS_PLUGIN_URL . 'assets/listado-noticias-widget.css',
                array(),
                '1.0.0'
            );
            
            // Encolar JavaScript
            wp_enqueue_script(
                'listado-noticias-widget-js',
                LISTADO_NOTICIAS_PLUGIN_URL . 'assets/listado-noticias-widget.js',
                array('jquery'),
                '1.0.0',
                true
            );
            
            // Localizar script para AJAX si es necesario
            wp_localize_script('listado-noticias-widget-js', 'listado_noticias_ajax', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('listado_noticias_nonce')
            ));
        }
    }

    /**
     * Frontend del widget
     */
    public function widget($args, $instance) {
        // Extraer argumentos
        echo $args['before_widget'];
        
        if (!empty($instance['title'])) {
            echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
        }

        // Obtener configuración
        $categoria_id = !empty($instance['categoria']) ? $instance['categoria'] : '';
        $numero_posts = !empty($instance['numero_posts']) ? intval($instance['numero_posts']) : 6;
        $mostrar_extracto = isset($instance['mostrar_extracto']) ? $instance['mostrar_extracto'] : false;
        $longitud_extracto = !empty($instance['longitud_extracto']) ? intval($instance['longitud_extracto']) : 100;
        $mostrar_fecha = isset($instance['mostrar_fecha']) ? $instance['mostrar_fecha'] : false;
        $orden = !empty($instance['orden']) ? $instance['orden'] : 'date';
        $direccion = !empty($instance['direccion']) ? $instance['direccion'] : 'DESC';

        // Query de posts
        $query_args = array(
            'post_type' => 'post',
            'posts_per_page' => $numero_posts,
            'post_status' => 'publish',
            'orderby' => $orden,
            'order' => $direccion
        );

        // Agregar filtro por categoría si está seleccionada
        if (!empty($categoria_id) && $categoria_id !== 'all') {
            $query_args['cat'] = $categoria_id;
        }

        $posts_query = new WP_Query($query_args);

        if ($posts_query->have_posts()) :
            $widget_id = 'listado-noticias-' . $this->number;
            ?>
            <div class="listado-noticias-container" id="<?php echo esc_attr($widget_id); ?>" 
                 data-categoria="<?php echo esc_attr($categoria_id); ?>"
                 data-posts="<?php echo esc_attr($numero_posts); ?>">
                <div class="listado-noticias-grid">
                    <?php 
                    $post_count = 0;
                    while ($posts_query->have_posts()) : 
                        $posts_query->the_post(); 
                        $post_count++;
                        $post_classes = 'noticia-item noticia-item-' . $post_count;
                        ?>
                        <article class="<?php echo esc_attr($post_classes); ?>" data-post-id="<?php echo get_the_ID(); ?>">
                            <a href="<?php the_permalink(); ?>" class="noticia-link">
                                <div class="noticia-imagen">
                                    <?php if (has_post_thumbnail()) : ?>
                                        <?php the_post_thumbnail('medium', array(
                                            'class' => 'noticia-thumbnail',
                                            'loading' => 'lazy'
                                        )); ?>
                                    <?php else : ?>
                                        <div class="sin-imagen">
                                            <i class="fas fa-image"></i>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <!-- Overlay para categoría -->
                                    <?php 
                                    $categories = get_the_category();
                                    if (!empty($categories)) :
                                        $primary_category = $categories[0];
                                    ?>
                                        <div class="noticia-categoria-overlay">
                                            <?php echo esc_html($primary_category->name); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="noticia-contenido">
                                    <h3 class="noticia-titulo">
                                        <?php the_title(); ?>
                                    </h3>
                                    
                                    <?php if ($mostrar_extracto) : ?>
                                        <div class="noticia-extracto">
                                            <?php echo wp_trim_words(get_the_excerpt(), ceil($longitud_extracto/6), '...'); ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if ($mostrar_fecha) : ?>
                                        <div class="noticia-meta">
                                            <time datetime="<?php echo get_the_date('c'); ?>">
                                                <?php echo get_the_date('d/m/Y'); ?>
                                            </time>
                                            <?php if (!empty($categories)) : ?>
                                                <span class="noticia-categoria-text">
                                                    en <?php echo esc_html($primary_category->name); ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </a>
                        </article>
                    <?php endwhile; ?>
                </div>
                
                <!-- Botón "Ver más" opcional -->
                <?php if ($numero_posts >= 6 && $posts_query->found_posts > $numero_posts) : ?>
                    <div class="listado-noticias-footer">
                        <button class="btn-ver-mas" data-widget-id="<?php echo esc_attr($widget_id); ?>">
                            Ver más noticias
                        </button>
                    </div>
                <?php endif; ?>
            </div>
            <?php
            wp_reset_postdata();
        else :
            echo '<div class="no-posts-message"><p>No se encontraron publicaciones para mostrar.</p></div>';
        endif;

        echo $args['after_widget'];
    }

    /**
     * Backend del widget (formulario de configuración)
     */
    public function form($instance) {
        // Valores por defecto
        $title = !empty($instance['title']) ? $instance['title'] : 'Últimas Noticias';
        $categoria = !empty($instance['categoria']) ? $instance['categoria'] : 'all';
        $numero_posts = !empty($instance['numero_posts']) ? $instance['numero_posts'] : 6;
        $mostrar_extracto = isset($instance['mostrar_extracto']) ? (bool) $instance['mostrar_extracto'] : false;
        $longitud_extracto = !empty($instance['longitud_extracto']) ? $instance['longitud_extracto'] : 100;
        $mostrar_fecha = isset($instance['mostrar_fecha']) ? (bool) $instance['mostrar_fecha'] : false;
        $orden = !empty($instance['orden']) ? $instance['orden'] : 'date';
        $direccion = !empty($instance['direccion']) ? $instance['direccion'] : 'DESC';
        
        // Obtener categorías
        $categorias = get_categories(array(
            'orderby' => 'name',
            'order' => 'ASC',
            'hide_empty' => false
        ));
        ?>
        
        <div class="listado-noticias-admin-form">
            <!-- Título del widget -->
            <p>
                <label for="<?php echo $this->get_field_id('title'); ?>">
                    <strong>Título del widget:</strong>
                </label>
                <input class="widefat" 
                       id="<?php echo $this->get_field_id('title'); ?>" 
                       name="<?php echo $this->get_field_name('title'); ?>" 
                       type="text" 
                       value="<?php echo esc_attr($title); ?>"
                       placeholder="Ej: Últimas Noticias">
            </p>

            <!-- Selector de categoría -->
            <p>
                <label for="<?php echo $this->get_field_id('categoria'); ?>">
                    <strong>Categoría a mostrar:</strong>
                </label>
                <select class="widefat" 
                        id="<?php echo $this->get_field_id('categoria'); ?>" 
                        name="<?php echo $this->get_field_name('categoria'); ?>">
                    <option value="all" <?php selected($categoria, 'all'); ?>>Todas las categorías</option>
                    <?php foreach ($categorias as $cat) : ?>
                        <option value="<?php echo $cat->term_id; ?>" <?php selected($categoria, $cat->term_id); ?>>
                            <?php echo esc_html($cat->name . ' (' . $cat->count . ')'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </p>

            <!-- Número de posts -->
            <p>
                <label for="<?php echo $this->get_field_id('numero_posts'); ?>">
                    <strong>Número de publicaciones:</strong>
                </label>
                <input class="tiny-text" 
                       id="<?php echo $this->get_field_id('numero_posts'); ?>" 
                       name="<?php echo $this->get_field_name('numero_posts'); ?>" 
                       type="number" 
                       step="1" 
                       min="1" 
                       max="20" 
                       value="<?php echo esc_attr($numero_posts); ?>">
                <small>Recomendado: 6 para replicar el formato original</small>
            </p>

            <!-- Ordenamiento -->
            <p>
                <label for="<?php echo $this->get_field_id('orden'); ?>">
                    <strong>Ordenar por:</strong>
                </label>
                <select class="widefat" 
                        id="<?php echo $this->get_field_id('orden'); ?>" 
                        name="<?php echo $this->get_field_name('orden'); ?>">
                    <option value="date" <?php selected($orden, 'date'); ?>>Fecha de publicación</option>
                    <option value="title" <?php selected($orden, 'title'); ?>>Título</option>
                    <option value="menu_order" <?php selected($orden, 'menu_order'); ?>>Orden personalizado</option>
                    <option value="rand" <?php selected($orden, 'rand'); ?>>Aleatorio</option>
                </select>
                
                <select class="widefat" style="margin-top: 5px;"
                        id="<?php echo $this->get_field_id('direccion'); ?>" 
                        name="<?php echo $this->get_field_name('direccion'); ?>">
                    <option value="DESC" <?php selected($direccion, 'DESC'); ?>>Descendente (más reciente primero)</option>
                    <option value="ASC" <?php selected($direccion, 'ASC'); ?>>Ascendente (más antiguo primero)</option>
                </select>
            </p>

            <!-- Opciones de visualización -->
            <p>
                <strong>Opciones de visualización:</strong><br>
                
                <label style="display: block; margin: 5px 0;">
                    <input class="checkbox" 
                           type="checkbox" 
                           <?php checked($mostrar_extracto); ?> 
                           id="<?php echo $this->get_field_id('mostrar_extracto'); ?>" 
                           name="<?php echo $this->get_field_name('mostrar_extracto'); ?>">
                    Mostrar extracto
                </label>
                
                <label style="display: block; margin: 5px 0;">
                    <input class="checkbox" 
                           type="checkbox" 
                           <?php checked($mostrar_fecha); ?> 
                           id="<?php echo $this->get_field_id('mostrar_fecha'); ?>" 
                           name="<?php echo $this->get_field_name('mostrar_fecha'); ?>">
                    Mostrar fecha de publicación
                </label>
            </p>

            <!-- Longitud del extracto -->
            <p>
                <label for="<?php echo $this->get_field_id('longitud_extracto'); ?>">
                    <strong>Longitud del extracto (caracteres):</strong>
                </label>
                <input class="tiny-text" 
                       id="<?php echo $this->get_field_id('longitud_extracto'); ?>" 
                       name="<?php echo $this->get_field_name('longitud_extracto'); ?>" 
                       type="number" 
                       step="10" 
                       min="50" 
                       max="300" 
                       value="<?php echo esc_attr($longitud_extracto); ?>">
            </p>
            
            <p style="background: #f9f9f9; padding: 10px; border-left: 3px solid #0073aa;">
                <small><strong>💡 Consejo:</strong> Para replicar exactamente el formato original de Drupal, deja desmarcadas las opciones de extracto y fecha.</small>
            </p>
        </div>
        
        <?php
    }

    /**
     * Guardar configuración del widget
     */
    public function update($new_instance, $old_instance) {
        $instance = array();
        
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        $instance['categoria'] = (!empty($new_instance['categoria'])) ? strip_tags($new_instance['categoria']) : 'all';
        $instance['numero_posts'] = (!empty($new_instance['numero_posts'])) ? intval($new_instance['numero_posts']) : 6;
        $instance['mostrar_extracto'] = isset($new_instance['mostrar_extracto']) ? (bool) $new_instance['mostrar_extracto'] : false;
        $instance['longitud_extracto'] = (!empty($new_instance['longitud_extracto'])) ? intval($new_instance['longitud_extracto']) : 100;
        $instance['mostrar_fecha'] = isset($new_instance['mostrar_fecha']) ? (bool) $new_instance['mostrar_fecha'] : false;
        $instance['orden'] = (!empty($new_instance['orden'])) ? strip_tags($new_instance['orden']) : 'date';
        $instance['direccion'] = (!empty($new_instance['direccion'])) ? strip_tags($new_instance['direccion']) : 'DESC';
        
        return $instance;
    }
}

/**
 * Registrar el widget
 */
function registrar_listado_noticias_widget() {
    register_widget('Listado_Noticias_Widget');
}
add_action('widgets_init', 'registrar_listado_noticias_widget');

/**
 * Shortcode para usar el widget en contenido
 */
function listado_noticias_shortcode($atts) {
    $atts = shortcode_atts(array(
        'categoria' => 'all',
        'numero' => 6,
        'titulo' => '',
        'extracto' => 'false',
        'longitud_extracto' => 100,
        'fecha' => 'false',
        'orden' => 'date',
        'direccion' => 'DESC'
    ), $atts);

    // Simular instancia del widget
    $instance = array(
        'title' => $atts['titulo'],
        'categoria' => $atts['categoria'],
        'numero_posts' => intval($atts['numero']),
        'mostrar_extracto' => ($atts['extracto'] === 'true'),
        'longitud_extracto' => intval($atts['longitud_extracto']),
        'mostrar_fecha' => ($atts['fecha'] === 'true'),
        'orden' => $atts['orden'],
        'direccion' => $atts['direccion']
    );

    // Capturar salida del widget
    ob_start();
    
    $widget = new Listado_Noticias_Widget();
    $widget->widget(array('before_widget' => '', 'after_widget' => '', 'before_title' => '<h3>', 'after_title' => '</h3>'), $instance);
    
    return ob_get_clean();
}
add_shortcode('listado_noticias', 'listado_noticias_shortcode');

/**
 * AJAX handler para cargar más posts
 */
function listado_noticias_load_more() {
    // Verificar nonce
    if (!wp_verify_nonce($_POST['nonce'], 'listado_noticias_nonce')) {
        wp_die('Acceso no autorizado');
    }

    $categoria = sanitize_text_field($_POST['categoria']);
    $posts_per_page = intval($_POST['posts_per_page']);
    $offset = intval($_POST['offset']);

    $query_args = array(
        'post_type' => 'post',
        'posts_per_page' => $posts_per_page,
        'offset' => $offset,
        'post_status' => 'publish',
        'orderby' => 'date',
        'order' => 'DESC'
    );

    if (!empty($categoria) && $categoria !== 'all') {
        $query_args['cat'] = $categoria;
    }

    $posts_query = new WP_Query($query_args);

    if ($posts_query->have_posts()) {
        while ($posts_query->have_posts()) {
            $posts_query->the_post();
            // Aquí puedes incluir el template del post
            echo '<div class="noticia-item-ajax">';
            echo '<h3>' . get_the_title() . '</h3>';
            echo '</div>';
        }
        wp_reset_postdata();
    }

    wp_die();
}
add_action('wp_ajax_listado_noticias_load_more', 'listado_noticias_load_more');
add_action('wp_ajax_nopriv_listado_noticias_load_more', 'listado_noticias_load_more');

/**
 * Agregar Font Awesome si no está cargado
 */
function listado_noticias_enqueue_fontawesome() {
    if (!wp_style_is('font-awesome', 'enqueued') && !wp_style_is('fontawesome', 'enqueued')) {
        wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css', array(), '6.0.0');
    }
}
add_action('wp_enqueue_scripts', 'listado_noticias_enqueue_fontawesome');

/**
 * Función helper para obtener estadísticas del widget
 */
function get_listado_noticias_stats() {
    $stats = array();
    
    // Total de posts
    $stats['total_posts'] = wp_count_posts()->publish;
    
    // Posts por categoría
    $categorias = get_categories(array('hide_empty' => false));
    $stats['categorias'] = array();
    
    foreach ($categorias as $categoria) {
        $stats['categorias'][$categoria->slug] = array(
            'name' => $categoria->name,
            'count' => $categoria->count
        );
    }
    
    return $stats;
}

/**
 * Hook de activación del plugin
 */
function listado_noticias_plugin_activation() {
    // Crear directorio de assets si no existe
    $upload_dir = wp_upload_dir();
    $assets_dir = LISTADO_NOTICIAS_PLUGIN_PATH . 'assets';
    
    if (!file_exists($assets_dir)) {
        wp_mkdir_p($assets_dir);
    }
}
register_activation_hook(__FILE__, 'listado_noticias_plugin_activation');
?>