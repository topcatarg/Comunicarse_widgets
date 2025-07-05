<?php
/**
 * Plugin Name: Hub Page Widgets
 * Description: Widget que replica el formato de hubs de comunicarseweb.com/hubs/
 * Version: 1.0.0
 * Author: Tu Nombre
 * Text Domain: hub-page-widgets
 */

// Evitar acceso directo
if (!defined('ABSPATH')) {
    exit;
}

// Definir constantes
define('HUB_PAGE_WIDGETS_VERSION', '1.0.0');
define('HUB_PAGE_WIDGETS_URL', plugin_dir_url(__FILE__));

/**
 * Clase principal del widget Hub
 */
class Hub_Page_Widget extends WP_Widget 
{
    public function __construct() 
    {
        parent::__construct(
            'hub_page_widget',
            __('Hub Page Widget', 'hub-page-widgets'),
            array(
                'description' => __('Replica el formato de hub de comunicarseweb.com', 'hub-page-widgets'),
                'classname' => 'hub-page-widget'
            )
        );
        
        add_action('wp_enqueue_scripts', array($this, 'enqueue_styles'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
    }

    /**
     * Frontend del widget
     */
    public function widget($args, $instance) 
    {
        echo $args['before_widget'];

        // Configuración
        $titulo_hub = !empty($instance['titulo_hub']) ? $instance['titulo_hub'] : 'Hub de Contenido';
        $categoria_id = !empty($instance['categoria']) ? intval($instance['categoria']) : '';
        $imagen_izquierda = !empty($instance['imagen_izquierda']) ? $instance['imagen_izquierda'] : '';
        $popup_titulo = !empty($instance['popup_titulo']) ? $instance['popup_titulo'] : '';
        $popup_contenido = !empty($instance['popup_contenido']) ? $instance['popup_contenido'] : '';
        $publicacion_destacada_id = !empty($instance['publicacion_destacada']) ? intval($instance['publicacion_destacada']) : '';
        $publicacion_sticky_id = !empty($instance['publicacion_sticky']) ? intval($instance['publicacion_sticky']) : '';
        $imagen_boton = !empty($instance['imagen_boton']) ? $instance['imagen_boton'] : '';
        $url_boton = !empty($instance['url_boton']) ? $instance['url_boton'] : '';
        $limite_publicaciones = !empty($instance['limite_publicaciones']) ? intval($instance['limite_publicaciones']) : 8;

        ?>
        <div class="hub-page-container">
            
            <!-- Título del Hub -->
            <div class="hub-header">
                <h2 class="hub-titulo"><?php echo esc_html($titulo_hub); ?></h2>
            </div>

            <!-- Layout principal de 3 columnas -->
            <div class="hub-layout">
                
                <!-- Columna izquierda: Imagen con popup -->
                <div class="hub-columna-izquierda">
                    <?php if ($imagen_izquierda): ?>
                        <div class="hub-imagen-container">
                            <img src="<?php echo esc_url($imagen_izquierda); ?>" alt="<?php echo esc_attr($titulo_hub); ?>" class="hub-imagen-principal">
                            
                            <?php if ($popup_titulo || $popup_contenido): ?>
                                <div class="hub-sobre-contenido">
                                    <span class="sobre-texto">Sobre este contenido</span>
                                    <div class="popup-overlay" style="display: none;">
                                        <div class="popup-contenido">
                                            <span class="popup-cerrar">&times;</span>
                                            <?php if ($popup_titulo): ?>
                                                <h3><?php echo esc_html($popup_titulo); ?></h3>
                                            <?php endif; ?>
                                            <?php if ($popup_contenido): ?>
                                                <div class="popup-texto"><?php echo wp_kses_post($popup_contenido); ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Columna central: Publicación destacada + Lista -->
                <div class="hub-columna-central">
                    
                    <!-- Publicación destacada -->
                    <?php if ($publicacion_destacada_id): ?>
                        <?php 
                        $post_destacado = get_post($publicacion_destacada_id);
                        if ($post_destacado): 
                        ?>
                            <article class="hub-post-destacado">
                                <a href="<?php echo get_permalink($post_destacado->ID); ?>" class="post-destacado-link">
                                    <?php if (has_post_thumbnail($post_destacado->ID)): ?>
                                        <div class="post-destacado-imagen">
                                            <?php echo get_the_post_thumbnail($post_destacado->ID, 'large'); ?>
                                        </div>
                                    <?php endif; ?>
                                    <div class="post-destacado-contenido">
                                        <h3 class="post-destacado-titulo"><?php echo esc_html($post_destacado->post_title); ?></h3>
                                        <div class="post-destacado-extracto">
                                            <?php 
                                            $extracto = $post_destacado->post_excerpt ?: $post_destacado->post_content;
                                            echo wp_trim_words($extracto, 25, '...');
                                            ?>
                                        </div>
                                    </div>
                                </a>
                            </article>
                        <?php endif; ?>
                    <?php endif; ?>

                    <!-- Lista de publicaciones -->
                    <div class="hub-lista-posts">
                        <?php
                        // Query para obtener publicaciones
                        $args_posts = array(
                            'post_type' => 'post',
                            'posts_per_page' => $limite_publicaciones,
                            'post_status' => 'publish',
                            'orderby' => 'date',
                            'order' => 'DESC'
                        );

                        // Filtrar por categoría si está seleccionada
                        if ($categoria_id && $categoria_id !== 'all') {
                            $args_posts['cat'] = $categoria_id;
                        }

                        // Excluir publicaciones ya mostradas
                        $excluir = array();
                        if ($publicacion_destacada_id) $excluir[] = $publicacion_destacada_id;
                        if ($publicacion_sticky_id) $excluir[] = $publicacion_sticky_id;
                        if (!empty($excluir)) {
                            $args_posts['post__not_in'] = $excluir;
                        }

                        $posts_query = new WP_Query($args_posts);
                        
                        if ($posts_query->have_posts()):
                        ?>
                            <div class="lista-articulos">
                                <?php while ($posts_query->have_posts()): $posts_query->the_post(); ?>
                                    <article class="articulo-item">
                                        <a href="<?php the_permalink(); ?>" class="articulo-link">
                                            <?php if (has_post_thumbnail()): ?>
                                                <div class="articulo-imagen">
                                                    <?php the_post_thumbnail('thumbnail'); ?>
                                                </div>
                                            <?php endif; ?>
                                            <div class="articulo-contenido">
                                                <h4 class="articulo-titulo"><?php the_title(); ?></h4>
                                                <div class="articulo-extracto">
                                                    <?php echo wp_trim_words(get_the_excerpt() ?: get_the_content(), 15, '...'); ?>
                                                </div>
                                            </div>
                                        </a>
                                    </article>
                                <?php endwhile; ?>
                            </div>
                            <?php wp_reset_postdata(); ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Columna derecha: Publicación sticky + Botón -->
                <div class="hub-columna-derecha">
                    
                    <!-- Publicación sticky -->
                    <?php if ($publicacion_sticky_id): ?>
                        <?php 
                        $post_sticky = get_post($publicacion_sticky_id);
                        if ($post_sticky): 
                        ?>
                            <article class="hub-post-sticky">
                                <a href="<?php echo get_permalink($post_sticky->ID); ?>" class="post-sticky-link">
                                    <?php if (has_post_thumbnail($post_sticky->ID)): ?>
                                        <div class="post-sticky-imagen">
                                            <?php echo get_the_post_thumbnail($post_sticky->ID, 'medium'); ?>
                                        </div>
                                    <?php endif; ?>
                                    <div class="post-sticky-contenido">
                                        <h3 class="post-sticky-titulo"><?php echo esc_html($post_sticky->post_title); ?></h3>
                                        <div class="post-sticky-extracto">
                                            <?php 
                                            $extracto = $post_sticky->post_excerpt ?: $post_sticky->post_content;
                                            echo wp_trim_words($extracto, 15, '...');
                                            ?>
                                        </div>
                                    </div>
                                </a>
                            </article>
                        <?php endif; ?>
                    <?php endif; ?>

                    <!-- Botón de redirección -->
                    <?php if ($imagen_boton && $url_boton): ?>
                        <div class="hub-boton-container">
                            <a href="<?php echo esc_url($url_boton); ?>" class="hub-boton-redireccion" target="_blank">
                                <img src="<?php echo esc_url($imagen_boton); ?>" alt="Redireccionar" class="boton-imagen">
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php

        echo $args['after_widget'];
    }

    /**
     * Formulario de configuración del widget
     */
    public function form($instance) 
    {
        // Valores por defecto
        $titulo_hub = !empty($instance['titulo_hub']) ? $instance['titulo_hub'] : 'Hub de Contenido';
        $categoria = !empty($instance['categoria']) ? $instance['categoria'] : 'all';
        $imagen_izquierda = !empty($instance['imagen_izquierda']) ? $instance['imagen_izquierda'] : '';
        $popup_titulo = !empty($instance['popup_titulo']) ? $instance['popup_titulo'] : '';
        $popup_contenido = !empty($instance['popup_contenido']) ? $instance['popup_contenido'] : '';
        $publicacion_destacada = !empty($instance['publicacion_destacada']) ? $instance['publicacion_destacada'] : '';
        $publicacion_sticky = !empty($instance['publicacion_sticky']) ? $instance['publicacion_sticky'] : '';
        $imagen_boton = !empty($instance['imagen_boton']) ? $instance['imagen_boton'] : '';
        $url_boton = !empty($instance['url_boton']) ? $instance['url_boton'] : '';
        $limite_publicaciones = !empty($instance['limite_publicaciones']) ? $instance['limite_publicaciones'] : 8;
        ?>

        <div class="hub-admin-form">
            
            <!-- Título del Hub -->
            <p>
                <label for="<?php echo $this->get_field_id('titulo_hub'); ?>">
                    <strong><?php _e('Título del Hub:', 'hub-page-widgets'); ?></strong>
                </label>
                <input class="widefat" 
                       id="<?php echo $this->get_field_id('titulo_hub'); ?>" 
                       name="<?php echo $this->get_field_name('titulo_hub'); ?>" 
                       type="text" 
                       value="<?php echo esc_attr($titulo_hub); ?>"
                       placeholder="Ej: COP Cambio Climático">
            </p>

            <!-- Categoría -->
            <p>
                <label for="<?php echo $this->get_field_id('categoria'); ?>">
                    <strong><?php _e('Categoría a mostrar:', 'hub-page-widgets'); ?></strong>
                </label>
                <select class="widefat" 
                        id="<?php echo $this->get_field_id('categoria'); ?>" 
                        name="<?php echo $this->get_field_name('categoria'); ?>">
                    <option value="all" <?php selected($categoria, 'all'); ?>>
                        <?php _e('Todas las categorías', 'hub-page-widgets'); ?>
                    </option>
                    <?php
                    $categorias = get_categories(array('hide_empty' => false));
                    foreach ($categorias as $cat) {
                        printf(
                            '<option value="%d" %s>%s</option>',
                            $cat->term_id,
                            selected($categoria, $cat->term_id, false),
                            esc_html($cat->name)
                        );
                    }
                    ?>
                </select>
            </p>

            <!-- Imagen izquierda -->
            <p>
                <label for="<?php echo $this->get_field_id('imagen_izquierda'); ?>">
                    <strong><?php _e('Imagen izquierda:', 'hub-page-widgets'); ?></strong>
                </label>
                <input class="widefat" 
                       id="<?php echo $this->get_field_id('imagen_izquierda'); ?>" 
                       name="<?php echo $this->get_field_name('imagen_izquierda'); ?>" 
                       type="url" 
                       value="<?php echo esc_attr($imagen_izquierda); ?>"
                       placeholder="URL de la imagen">
                <button type="button" class="button hub-select-image" 
                        data-target="<?php echo $this->get_field_id('imagen_izquierda'); ?>">
                    <?php _e('Seleccionar imagen', 'hub-page-widgets'); ?>
                </button>
            </p>

            <!-- Configuración del popup -->
            <fieldset style="border: 1px solid #ddd; padding: 10px; margin: 10px 0;">
                <legend><strong><?php _e('Popup "Sobre este contenido":', 'hub-page-widgets'); ?></strong></legend>
                
                <p>
                    <label for="<?php echo $this->get_field_id('popup_titulo'); ?>">
                        <?php _e('Título del popup:', 'hub-page-widgets'); ?>
                    </label>
                    <input class="widefat" 
                           id="<?php echo $this->get_field_id('popup_titulo'); ?>" 
                           name="<?php echo $this->get_field_name('popup_titulo'); ?>" 
                           type="text" 
                           value="<?php echo esc_attr($popup_titulo); ?>"
                           placeholder="Título del popup">
                </p>

                <p>
                    <label for="<?php echo $this->get_field_id('popup_contenido'); ?>">
                        <?php _e('Contenido del popup:', 'hub-page-widgets'); ?>
                    </label>
                    <textarea class="widefat" 
                              id="<?php echo $this->get_field_id('popup_contenido'); ?>" 
                              name="<?php echo $this->get_field_name('popup_contenido'); ?>" 
                              rows="4"
                              placeholder="Descripción que aparecerá en el popup"><?php echo esc_textarea($popup_contenido); ?></textarea>
                </p>
            </fieldset>

            <!-- Publicación destacada (central superior) -->
            <p>
                <label for="<?php echo $this->get_field_id('publicacion_destacada'); ?>">
                    <strong><?php _e('Publicación destacada (centro superior):', 'hub-page-widgets'); ?></strong>
                </label>
                <select class="widefat" 
                        id="<?php echo $this->get_field_id('publicacion_destacada'); ?>" 
                        name="<?php echo $this->get_field_name('publicacion_destacada'); ?>">
                    <option value=""><?php _e('Ninguna', 'hub-page-widgets'); ?></option>
                    <?php
                    $posts = get_posts(array(
                        'numberposts' => 30,
                        'post_status' => 'publish',
                        'orderby' => 'date',
                        'order' => 'DESC'
                    ));
                    foreach ($posts as $post) {
                        printf(
                            '<option value="%d" %s>%s</option>',
                            $post->ID,
                            selected($publicacion_destacada, $post->ID, false),
                            esc_html($post->post_title)
                        );
                    }
                    ?>
                </select>
            </p>

            <!-- Publicación sticky (derecha) -->
            <p>
                <label for="<?php echo $this->get_field_id('publicacion_sticky'); ?>">
                    <strong><?php _e('Publicación sticky (columna derecha):', 'hub-page-widgets'); ?></strong>
                </label>
                <select class="widefat" 
                        id="<?php echo $this->get_field_id('publicacion_sticky'); ?>" 
                        name="<?php echo $this->get_field_name('publicacion_sticky'); ?>">
                    <option value=""><?php _e('Ninguna', 'hub-page-widgets'); ?></option>
                    <?php
                    foreach ($posts as $post) {
                        printf(
                            '<option value="%d" %s>%s</option>',
                            $post->ID,
                            selected($publicacion_sticky, $post->ID, false),
                            esc_html($post->post_title)
                        );
                    }
                    ?>
                </select>
            </p>

            <!-- Límite de publicaciones en lista -->
            <p>
                <label for="<?php echo $this->get_field_id('limite_publicaciones'); ?>">
                    <strong><?php _e('Número de publicaciones en lista:', 'hub-page-widgets'); ?></strong>
                </label>
                <input class="small-text" 
                       id="<?php echo $this->get_field_id('limite_publicaciones'); ?>" 
                       name="<?php echo $this->get_field_name('limite_publicaciones'); ?>" 
                       type="number" 
                       min="3" 
                       max="15"
                       value="<?php echo esc_attr($limite_publicaciones); ?>">
                <small class="description"><?php _e('Entre 3 y 15 publicaciones', 'hub-page-widgets'); ?></small>
            </p>

            <!-- Botón de redirección -->
            <fieldset style="border: 1px solid #ddd; padding: 10px; margin: 10px 0;">
                <legend><strong><?php _e('Botón de redirección (abajo derecha):', 'hub-page-widgets'); ?></strong></legend>
                
                <p>
                    <label for="<?php echo $this->get_field_id('imagen_boton'); ?>">
                        <?php _e('Imagen del botón:', 'hub-page-widgets'); ?>
                    </label>
                    <input class="widefat" 
                           id="<?php echo $this->get_field_id('imagen_boton'); ?>" 
                           name="<?php echo $this->get_field_name('imagen_boton'); ?>" 
                           type="url" 
                           value="<?php echo esc_attr($imagen_boton); ?>"
                           placeholder="URL de la imagen del botón">
                    <button type="button" class="button hub-select-image" 
                            data-target="<?php echo $this->get_field_id('imagen_boton'); ?>">
                        <?php _e('Seleccionar imagen', 'hub-page-widgets'); ?>
                    </button>
                </p>

                <p>
                    <label for="<?php echo $this->get_field_id('url_boton'); ?>">
                        <?php _e('URL de destino:', 'hub-page-widgets'); ?>
                    </label>
                    <input class="widefat" 
                           id="<?php echo $this->get_field_id('url_boton'); ?>" 
                           name="<?php echo $this->get_field_name('url_boton'); ?>" 
                           type="url" 
                           value="<?php echo esc_attr($url_boton); ?>"
                           placeholder="https://ejemplo.com">
                </p>
            </fieldset>
        </div>
        <?php
    }

    /**
     * Guardar configuración del widget
     */
    public function update($new_instance, $old_instance) 
    {
        $instance = array();
        
        $instance['titulo_hub'] = sanitize_text_field($new_instance['titulo_hub']);
        $instance['categoria'] = sanitize_text_field($new_instance['categoria']);
        $instance['imagen_izquierda'] = esc_url_raw($new_instance['imagen_izquierda']);
        $instance['popup_titulo'] = sanitize_text_field($new_instance['popup_titulo']);
        $instance['popup_contenido'] = wp_kses_post($new_instance['popup_contenido']);
        $instance['publicacion_destacada'] = intval($new_instance['publicacion_destacada']);
        $instance['publicacion_sticky'] = intval($new_instance['publicacion_sticky']);
        $instance['limite_publicaciones'] = max(3, min(15, intval($new_instance['limite_publicaciones'])));
        $instance['imagen_boton'] = esc_url_raw($new_instance['imagen_boton']);
        $instance['url_boton'] = esc_url_raw($new_instance['url_boton']);
        
        return $instance;
    }

    /**
     * Cargar estilos
     */
    public function enqueue_styles() 
    {
        wp_enqueue_style(
            'hub-page-widgets-style',
            HUB_PAGE_WIDGETS_URL . 'style.css',
            array(),
            HUB_PAGE_WIDGETS_VERSION
        );
        
        wp_enqueue_script(
            'hub-page-widgets-script',
            HUB_PAGE_WIDGETS_URL . 'script.js',
            array('jquery'),
            HUB_PAGE_WIDGETS_VERSION,
            true
        );
    }

    /**
     * Scripts del admin
     */
    public function enqueue_admin_scripts($hook) 
    {
        if ($hook !== 'widgets.php') {
            return;
        }
        
        wp_enqueue_media();
        wp_enqueue_script(
            'hub-page-widgets-admin',
            HUB_PAGE_WIDGETS_URL . 'admin.js',
            array('jquery', 'media-upload'),
            HUB_PAGE_WIDGETS_VERSION,
            true
        );
    }
}

// Registrar el widget
function hub_page_registrar_widget() 
{
    register_widget('Hub_Page_Widget');
}
add_action('widgets_init', 'hub_page_registrar_widget');

// Shortcode
function hub_page_shortcode($atts) 
{
    $atts = shortcode_atts(array(
        'titulo_hub' => 'Hub de Contenido',
        'categoria' => 'all',
        'imagen_izquierda' => '',
        'popup_titulo' => '',
        'popup_contenido' => '',
        'publicacion_destacada' => '',
        'publicacion_sticky' => '',
        'limite_publicaciones' => 8,
        'imagen_boton' => '',
        'url_boton' => ''
    ), $atts);

    ob_start();
    
    $widget = new Hub_Page_Widget();
    $widget->widget(
        array('before_widget' => '', 'after_widget' => ''), 
        $atts
    );
    
    return ob_get_clean();
}
add_shortcode('hub_page', 'hub_page_shortcode');

// Crear tabla de configuraciones al activar el plugin (opcional)
function hub_page_create_table() 
{
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'hub_page_configs';
    
    $charset_collate = $wpdb->get_charset_collate();
    
    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        nombre varchar(100) NOT NULL,
        config longtext NOT NULL,
        fecha_creacion datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) $charset_collate;";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

// Limpiar widgets antiguos al activar
function hub_page_cleanup_old_widgets() {
    // Remover referencias a la clase antigua
    $widgets = get_option('widget_comunicarse_hub_widget', array());
    if (!empty($widgets)) {
        delete_option('widget_comunicarse_hub_widget');
    }
}

register_activation_hook(__FILE__, 'hub_page_create_table');
register_activation_hook(__FILE__, 'hub_page_cleanup_old_widgets');
?>