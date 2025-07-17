<?php
/**
 * Plugin Name: Opinion Widget V2 Fixed
 * Plugin URI: https://comunicarseweb.com
 * Description: Widget V2 para posts tipo "opinion" - Versión corregida y simplificada
 * Version: 2.2.0
 * Author: Gonzalo Bianchi
 * License: GPL v2 or later
 */

// Prevenir acceso directo
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Widget Class V2 Fixed
 */
class Opinion_Widget_V2_Fixed extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'opinion_widget_v2_fixed',
            '🎯 Opinion V2 Fixed',
            array(
                'classname' => 'opinion-widget-v2-fixed',
                'description' => 'Widget simplificado para posts tipo "opinion" - Sin fecha ni extracto'
            )
        );
    }

    /**
     * Frontend del widget
     */
    public function widget($args, $instance) {
        // Verificar que existe el post type
        if (!post_type_exists('opinion')) {
            return;
        }

        echo $args['before_widget'];

        // Título
        if (!empty($instance['title'])) {
            echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
        }

        // Obtener opiniones
        $posts_count = !empty($instance['posts_count']) ? intval($instance['posts_count']) : 2;
        $category_filter = !empty($instance['category_filter']) ? $instance['category_filter'] : '';
        $show_more_url = !empty($instance['show_more_url']) ? $instance['show_more_url'] : '';

        $query_args = array(
            'post_type' => 'opinion',
            'posts_per_page' => $posts_count,
            'post_status' => 'publish',
            'orderby' => 'date',
            'order' => 'DESC'
        );

        // Filtro por categoría si existe
        if (!empty($category_filter) && taxonomy_exists('categoria_opinion')) {
            $query_args['tax_query'] = array(
                array(
                    'taxonomy' => 'categoria_opinion',
                    'field' => 'term_id',
                    'terms' => intval($category_filter)
                )
            );
        }

        $opinion_posts = get_posts($query_args);

        if (empty($opinion_posts)) {
            echo '<p class="no-opinions">No hay opiniones disponibles.</p>';
            echo $args['after_widget'];
            return;
        }

        // Layout
        $layout = !empty($instance['layout']) ? $instance['layout'] : 'vertical';
        ?>

        <div class="opinion-widget-v2-fixed <?php echo esc_attr('layout-' . $layout); ?>">
            <?php foreach ($opinion_posts as $post): ?>
                <article class="opinion-item" data-id="<?php echo $post->ID; ?>">
                    
                    <!-- Autor info - Primera fila -->
                    <div class="opinion-author-box">
                        <?php 
                        // Obtener metadatos
                        $author_name = get_post_meta($post->ID, '_mh_custom_author_name', true);
                        $author_title = get_post_meta($post->ID, '_mh_author_title', true);
                        $author_photo_id = get_post_meta($post->ID, '_mh_author_photo_fid', true);
                        $opinion_quote = get_post_meta($post->ID, '_mh_opinion_quote', true);
                        ?>

                        <div class="author-info">
                            <?php if ($author_photo_id): 
                                $photo = wp_get_attachment_image($author_photo_id, 'thumbnail', false, array('class' => 'author-photo'));
                                if ($photo): ?>
                                <div class="photo-container"><?php echo $photo; ?></div>
                                <?php endif;
                            endif; ?>

                            <div class="author-text">
                                <?php if ($author_name): ?>
                                    <div class="author-name"><?php echo esc_html($author_name); ?></div>
                                <?php endif; ?>
                                <?php if ($author_title): ?>
                                    <div class="author-position"><?php echo esc_html($author_title); ?></div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <?php if ($opinion_quote): ?>
                            <div class="opinion-quote">
                                <a href="<?php echo get_permalink($post->ID); ?>">
                                    <blockquote>"<?php echo esc_html($opinion_quote); ?>"</blockquote>
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>

                </article>
            <?php endforeach; ?>
            
            <?php if (!empty($show_more_url)): ?>
                <div class="opinion-more-button">
                    <a href="<?php echo esc_url($show_more_url); ?>" >
                        Ver más opiniones
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <style>
        .opinion-widget-v2-fixed {
            margin: 1rem 0;
        }

        .opinion-widget-v2-fixed .opinion-item {
            background: linear-gradient(135deg, #f8f9fa 0%, #fff 100%);
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border-left: 4px solid #0073aa;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
        }

        .opinion-widget-v2-fixed .opinion-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.12);
        }

        .opinion-widget-v2-fixed .opinion-item:last-child {
            margin-bottom: 0;
        }

        .opinion-widget-v2-fixed .opinion-author-box {
            background: rgba(255,255,255,0.7);
            border-radius: 8px;
            padding: 1rem;
            border: 1px solid rgba(0,115,170,0.1);
        }

        .opinion-widget-v2-fixed .author-info {
            display: grid;
            grid-template-columns: 80px 1fr;
            gap: 1rem;
            align-items: center;
            margin-bottom: 1rem;
        }

        .opinion-widget-v2-fixed .photo-container {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .opinion-widget-v2-fixed .photo-container .author-photo {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #fff;
            box-shadow: 0 3px 12px rgba(0,0,0,0.15);
        }

        .opinion-widget-v2-fixed .author-text {
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .opinion-widget-v2-fixed .author-name {
            font-weight: 600;
            color: #333;
            margin-bottom: 2px;
            font-size: 1.1rem;
        }

        .opinion-widget-v2-fixed .author-position {
            font-size: 0.9rem;
            color: #666;
            font-style: italic;
        }

        .opinion-widget-v2-fixed .opinion-quote {
            border-top: 1px solid #eee;
            padding-top: 1rem;
        }

        .opinion-widget-v2-fixed .opinion-quote a {
            text-decoration: none;
            color: inherit;
        }

        .opinion-widget-v2-fixed .opinion-quote blockquote {
            margin: 0;
            font-style: italic;
            color: #0073aa;
            font-size: 1rem;
            line-height: 1.4;
            padding: 0.8rem;
            background: rgba(0,115,170,0.05);
            border-left: 3px solid #0073aa;
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }

        .opinion-widget-v2-fixed .opinion-quote a:hover blockquote {
            background: rgba(0,115,170,0.1);
        }

.opinion-widget-v2-fixed .opinion-more-button {
    text-align: center;
    margin-top: 1.5rem;
    padding-top: 1rem;
    border-top: 1px solid #eee;
}

.opinion-widget-v2-fixed .opinion-more-button a {
    display: inline-block;
    background: #0073aa;
    color: white;
    padding: 12px 24px;
    text-decoration: none;
    border-radius: 6px;
    font-weight: 500;
    transition: background-color 0.3s ease;
    border: none;
    cursor: pointer;
}

.opinion-widget-v2-fixed .opinion-more-button a:hover {
    background: #005a87;
    color: white;
    text-decoration: none;
}

.opinion-widget-v2-fixed .opinion-more-button a:focus {
    background: #004577;
    outline: 2px solid #87ceeb;
    outline-offset: 2px;
}

.opinion-widget-v2-fixed .opinion-more-button a:active {
    background: #004577;
}



        .opinion-widget-v2-fixed.layout-horizontal {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1rem;
        }

        .opinion-widget-v2-fixed.layout-horizontal .opinion-item {
            margin-bottom: 0;
        }

        .opinion-widget-v2-fixed.layout-horizontal .opinion-more-button {
            grid-column: 1 / -1;
        }

        .opinion-widget-v2-fixed .no-opinions {
            text-align: center;
            color: #666;
            font-style: italic;
            padding: 2rem;
            background: #f8f9fa;
            border-radius: 8px;
        }

        @media (max-width: 768px) {
            .opinion-widget-v2-fixed .author-info {
                grid-template-columns: 60px 1fr;
                gap: 0.8rem;
            }
            
            .opinion-widget-v2-fixed .photo-container .author-photo {
                width: 50px;
                height: 50px;
            }
            
            .opinion-widget-v2-fixed.layout-horizontal {
                grid-template-columns: 1fr;
            }
        }
        </style>

        <?php
        echo $args['after_widget'];
    }

    /**
     * Formulario admin
     */
    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : 'Últimas Opiniones';
        $posts_count = !empty($instance['posts_count']) ? $instance['posts_count'] : 2;
        $category_filter = !empty($instance['category_filter']) ? $instance['category_filter'] : '';
        $layout = !empty($instance['layout']) ? $instance['layout'] : 'vertical';
        $show_more_url = !empty($instance['show_more_url']) ? $instance['show_more_url'] : '';
        ?>
        
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>">
                <strong>Título:</strong>
            </label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" 
                   name="<?php echo $this->get_field_name('title'); ?>" type="text" 
                   value="<?php echo esc_attr($title); ?>">
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('posts_count'); ?>">
                <strong>Número de opiniones:</strong>
            </label>
            <select class="widefat" id="<?php echo $this->get_field_id('posts_count'); ?>" 
                    name="<?php echo $this->get_field_name('posts_count'); ?>">
                <?php for ($i = 1; $i <= 6; $i++): ?>
                    <option value="<?php echo $i; ?>" <?php selected($posts_count, $i); ?>>
                        <?php echo $i; ?> <?php echo ($i == 1) ? 'opinión' : 'opiniones'; ?>
                    </option>
                <?php endfor; ?>
            </select>
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('layout'); ?>">
                <strong>Diseño:</strong>
            </label>
            <select class="widefat" id="<?php echo $this->get_field_id('layout'); ?>" 
                    name="<?php echo $this->get_field_name('layout'); ?>">
                <option value="vertical" <?php selected($layout, 'vertical'); ?>>Vertical</option>
                <option value="horizontal" <?php selected($layout, 'horizontal'); ?>>Horizontal</option>
            </select>
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('show_more_url'); ?>">
                <strong>URL "Ver más":</strong>
            </label>
            <input class="widefat" id="<?php echo $this->get_field_id('show_more_url'); ?>" 
                   name="<?php echo $this->get_field_name('show_more_url'); ?>" type="url" 
                   value="<?php echo esc_url($show_more_url); ?>" 
                   placeholder="https://ejemplo.com/opiniones">
            <small>URL que enlaza a la lista completa de opiniones</small>
        </p>

        <?php if (taxonomy_exists('categoria_opinion')): ?>
        <p>
            <label for="<?php echo $this->get_field_id('category_filter'); ?>">
                <strong>Categoría:</strong>
            </label>
            <select class="widefat" id="<?php echo $this->get_field_id('category_filter'); ?>" 
                    name="<?php echo $this->get_field_name('category_filter'); ?>">
                <option value="">-- Todas --</option>
                <?php
                $categories = get_terms(array(
                    'taxonomy' => 'categoria_opinion',
                    'hide_empty' => false
                ));
                if (!is_wp_error($categories)) {
                    foreach ($categories as $term) {
                        $selected = ($category_filter == $term->term_id) ? 'selected' : '';
                        echo '<option value="' . $term->term_id . '" ' . $selected . '>' . $term->name . '</option>';
                    }
                }
                ?>
            </select>
        </p>
        <?php endif; ?>

        <div style="background: #e7f3ff; padding: 10px; border-radius: 4px; margin-top: 15px;">
            <strong>📋 Info V2 Fixed:</strong><br>
            • Solo posts tipo "opinion"<br>
            • Sin título de publicación<br>
            • Primera fila: imagen y datos del autor<br>
            • Botón "Ver más" configurable<br>
            • Diseño limpio y responsive
        </div>
        <?php
    }

    /**
     * Guardar configuración
     */
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        $instance['posts_count'] = (!empty($new_instance['posts_count'])) ? intval($new_instance['posts_count']) : 2;
        $instance['category_filter'] = (!empty($new_instance['category_filter'])) ? strip_tags($new_instance['category_filter']) : '';
        $instance['layout'] = (!empty($new_instance['layout'])) ? strip_tags($new_instance['layout']) : 'vertical';
        $instance['show_more_url'] = (!empty($new_instance['show_more_url'])) ? esc_url_raw($new_instance['show_more_url']) : '';
        
        return $instance;
    }
}

// Registrar widget
function register_opinion_widget_v2_fixed() {
    register_widget('Opinion_Widget_V2_Fixed');
}
add_action('widgets_init', 'register_opinion_widget_v2_fixed');

// Shortcode actualizado
function opinion_v2_fixed_shortcode($atts) {
    $atts = shortcode_atts(array(
        'count' => 2,
        'category' => '',
        'layout' => 'vertical',
        'show_more_url' => ''
    ), $atts);

    $widget = new Opinion_Widget_V2_Fixed();
    $instance = array(
        'posts_count' => intval($atts['count']),
        'category_filter' => $atts['category'],
        'layout' => $atts['layout'],
        'show_more_url' => $atts['show_more_url']
    );

    ob_start();
    $widget->widget(array(
        'before_widget' => '<div class="opinion-shortcode">',
        'after_widget' => '</div>',
        'before_title' => '<h3>',
        'after_title' => '</h3>'
    ), $instance);
    return ob_get_clean();
}
add_shortcode('opinion_v2_fixed', 'opinion_v2_fixed_shortcode');
?>