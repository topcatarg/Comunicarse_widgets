<?php
/**
 * Plugin Name: Publicaciones Destacadas Widget
 * Description: Widget que muestra 6 publicaciones destacadas con fondo azul, imagen, título y extracto
 * Version: 1.0
 * Author: Tu Nombre
 */

// Evitar acceso directo
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Clase principal del widget
 */
class Publicaciones_Destacadas_Widget extends WP_Widget {

    /**
     * Constructor del widget
     */
    public function __construct() {
        $widget_options = array(
            'classname' => 'publicaciones_destacadas_widget',
            'description' => 'Muestra 6 publicaciones destacadas con fondo azul'
        );
        
        parent::__construct(
            'publicaciones_destacadas_widget', 
            'Publicaciones Destacadas',
            $widget_options
        );
        
        // Agregar estilos CSS
        add_action('wp_enqueue_scripts', array($this, 'enqueue_styles'));
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
        $mostrar_extracto = isset($instance['mostrar_extracto']) ? $instance['mostrar_extracto'] : true;
        $longitud_extracto = !empty($instance['longitud_extracto']) ? intval($instance['longitud_extracto']) : 100;

        // Query de posts
        $query_args = array(
            'post_type' => 'post',
            'posts_per_page' => $numero_posts,
            'post_status' => 'publish',
            'orderby' => 'date',
            'order' => 'DESC'
        );

        // Agregar filtro por categoría si está seleccionada
        if (!empty($categoria_id) && $categoria_id !== 'all') {
            $query_args['cat'] = $categoria_id;
        }

        $posts_query = new WP_Query($query_args);

        if ($posts_query->have_posts()) :
            ?>
            <div class="publicaciones-destacadas-container">
                <div class="publicaciones-destacadas-grid">
                    <?php while ($posts_query->have_posts()) : $posts_query->the_post(); ?>
                        <article class="publicacion-destacada-item">
                            <a href="<?php the_permalink(); ?>" class="publicacion-destacada-link">
                                <div class="publicacion-destacada-imagen">
                                    <?php if (has_post_thumbnail()) : ?>
                                        <?php the_post_thumbnail('medium', array('class' => 'destacada-thumbnail')); ?>
                                    <?php else : ?>
                                        <div class="sin-imagen">
                                            <i class="fas fa-image"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="publicacion-destacada-contenido">
                                    <h3 class="publicacion-destacada-titulo">
                                        <?php the_title(); ?>
                                    </h3>
                                    
                                    <?php if ($mostrar_extracto) : ?>
                                        <div class="publicacion-destacada-extracto">
                                            <?php echo wp_trim_words(get_the_excerpt(), ceil($longitud_extracto/6), '...'); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </a>
                        </article>
                    <?php endwhile; ?>
                </div>
            </div>
            <?php
            wp_reset_postdata();
        else :
            echo '<p class="no-posts-message">No se encontraron publicaciones para mostrar.</p>';
        endif;

        echo $args['after_widget'];
    }

    /**
     * Backend del widget (formulario de configuración)
     */
    public function form($instance) {
        // Valores por defecto
        $title = !empty($instance['title']) ? $instance['title'] : 'Publicaciones Destacadas';
        $categoria = !empty($instance['categoria']) ? $instance['categoria'] : 'all';
        $numero_posts = !empty($instance['numero_posts']) ? $instance['numero_posts'] : 6;
        $mostrar_extracto = isset($instance['mostrar_extracto']) ? (bool) $instance['mostrar_extracto'] : true;
        $longitud_extracto = !empty($instance['longitud_extracto']) ? $instance['longitud_extracto'] : 100;
        
        // Obtener categorías
        $categorias = get_categories(array(
            'orderby' => 'name',
            'order' => 'ASC',
            'hide_empty' => false
        ));
        ?>
        
        <!-- Título del widget -->
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>">Título:</label>
            <input class="widefat" 
                   id="<?php echo $this->get_field_id('title'); ?>" 
                   name="<?php echo $this->get_field_name('title'); ?>" 
                   type="text" 
                   value="<?php echo esc_attr($title); ?>">
        </p>

        <!-- Selector de categoría -->
        <p>
            <label for="<?php echo $this->get_field_id('categoria'); ?>">Categoría:</label>
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
            <label for="<?php echo $this->get_field_id('numero_posts'); ?>">Número de publicaciones:</label>
            <input class="small-text" 
                   id="<?php echo $this->get_field_id('numero_posts'); ?>" 
                   name="<?php echo $this->get_field_name('numero_posts'); ?>" 
                   type="number" 
                   min="1" 
                   max="12" 
                   value="<?php echo esc_attr($numero_posts); ?>">
            <small>Máximo 12 publicaciones</small>
        </p>

        <!-- Mostrar extracto -->
        <p>
            <input class="checkbox" 
                   type="checkbox" 
                   <?php checked($mostrar_extracto); ?> 
                   id="<?php echo $this->get_field_id('mostrar_extracto'); ?>" 
                   name="<?php echo $this->get_field_name('mostrar_extracto'); ?>" />
            <label for="<?php echo $this->get_field_id('mostrar_extracto'); ?>">Mostrar extracto</label>
        </p>

        <!-- Longitud del extracto -->
        <p>
            <label for="<?php echo $this->get_field_id('longitud_extracto'); ?>">Longitud del extracto (caracteres):</label>
            <input class="small-text" 
                   id="<?php echo $this->get_field_id('longitud_extracto'); ?>" 
                   name="<?php echo $this->get_field_name('longitud_extracto'); ?>" 
                   type="number" 
                   min="50" 
                   max="300" 
                   value="<?php echo esc_attr($longitud_extracto); ?>">
        </p>

        <?php
    }

    /**
     * Actualizar configuración del widget
     */
    public function update($new_instance, $old_instance) {
        $instance = array();
        
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        $instance['categoria'] = (!empty($new_instance['categoria'])) ? $new_instance['categoria'] : 'all';
        $instance['numero_posts'] = (!empty($new_instance['numero_posts'])) ? intval($new_instance['numero_posts']) : 6;
        $instance['mostrar_extracto'] = (!empty($new_instance['mostrar_extracto'])) ? 1 : 0;
        $instance['longitud_extracto'] = (!empty($new_instance['longitud_extracto'])) ? intval($new_instance['longitud_extracto']) : 100;
        
        return $instance;
    }

    /**
     * Cargar estilos CSS
     */
    public function enqueue_styles() {
        wp_add_inline_style('wp-block-library', $this->get_widget_css());
    }

    /**
     * CSS del widget
     */
    private function get_widget_css() {
        return "
        .publicaciones-destacadas-container {
            width: 100%;
            margin: 20px 0;
        }

        .publicaciones-destacadas-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            grid-template-rows: repeat(3, 1fr);
            gap: 20px;
            margin: 20px 0;
        }

        @media (max-width: 767px) {
            .publicaciones-destacadas-grid {
                grid-template-columns: 1fr;
                grid-template-rows: auto;
            }
        }

        .publicacion-destacada-item {
            background: linear-gradient(135deg, #2E86AB 0%, #1A5276 100%);
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(46, 134, 171, 0.2);
            height: 400px;
            display: flex;
            flex-direction: column;
        }

        .publicacion-destacada-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(46, 134, 171, 0.3);
        }

        .publicacion-destacada-link {
            text-decoration: none;
            color: inherit;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .publicacion-destacada-imagen {
            height: 180px;
            overflow: hidden;
            position: relative;
        }

        .destacada-thumbnail {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .publicacion-destacada-item:hover .destacada-thumbnail {
            transform: scale(1.05);
        }

        .sin-imagen {
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            color: rgba(255, 255, 255, 0.6);
            font-size: 3em;
        }

        .publicacion-destacada-contenido {
            padding: 20px;
            color: white;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .publicacion-destacada-titulo {
            font-size: 16px;
            font-weight: 600;
            line-height: 1.4;
            margin: 0 0 12px 0;
            color: white;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .publicacion-destacada-extracto {
            font-size: 14px;
            line-height: 1.5;
            color: rgba(255, 255, 255, 0.9);
            display: -webkit-box;
            -webkit-line-clamp: 4;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            flex: 1;
        }

        /* Responsive mejorado */
        @media (max-width: 767px) {
            .publicaciones-destacadas-grid {
                grid-template-columns: 1fr;
                grid-template-rows: auto;
                gap: 15px;
            }
            
            .publicacion-destacada-item {
                height: 350px;
            }
            
            .publicacion-destacada-imagen {
                height: 150px;
            }
            
            .publicacion-destacada-contenido {
                padding: 15px;
            }
            
            .publicacion-destacada-titulo {
                font-size: 15px;
            }
            
            .publicacion-destacada-extracto {
                font-size: 13px;
            }
        }

        /* Efecto de hover mejorado */
        .publicacion-destacada-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.05) 100%);
            opacity: 0;
            transition: opacity 0.3s ease;
            pointer-events: none;
            border-radius: 12px;
        }

        .publicacion-destacada-item:hover::before {
            opacity: 1;
        }
        ";
    }
}

/**
 * Registrar el widget
 */
function registrar_publicaciones_destacadas_widget() {
    register_widget('Publicaciones_Destacadas_Widget');
}
add_action('widgets_init', 'registrar_publicaciones_destacadas_widget');

/**
 * Shortcode para usar en contenido
 */
function publicaciones_destacadas_shortcode($atts) {
    $atts = shortcode_atts(array(
        'categoria' => 'all',
        'numero' => 6,
        'extracto' => 'true',
        'longitud_extracto' => 100,
        'titulo' => ''
    ), $atts);

    // Simular instancia del widget
    $instance = array(
        'title' => $atts['titulo'],
        'categoria' => $atts['categoria'],
        'numero_posts' => intval($atts['numero']),
        'mostrar_extracto' => ($atts['extracto'] === 'true'),
        'longitud_extracto' => intval($atts['longitud_extracto'])
    );

    // Capturar salida del widget
    ob_start();
    
    $widget = new Publicaciones_Destacadas_Widget();
    $widget->widget(array('before_widget' => '', 'after_widget' => '', 'before_title' => '<h3>', 'after_title' => '</h3>'), $instance);
    
    return ob_get_clean();
}
add_shortcode('publicaciones_destacadas', 'publicaciones_destacadas_shortcode');

/**
 * Agregar Font Awesome si no está cargado
 */
function publicaciones_destacadas_enqueue_fontawesome() {
    if (!wp_style_is('font-awesome', 'enqueued')) {
        wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css');
    }
}
add_action('wp_enqueue_scripts', 'publicaciones_destacadas_enqueue_fontawesome');

/**
 * Función helper para obtener estadísticas del widget (opcional)
 */
function get_publicaciones_destacadas_stats() {
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
?>