<?php
/**
 * Plugin Name: Sección de Opinión
 * Description: Widget que muestra automáticamente los últimos 2 posts de opinión
 * Version: 1.0
 * Author: Tu Nombre
 */

// Evitar acceso directo
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Widget para Sección de Opinión
 * Muestra automáticamente los últimos 2 posts de opinión con metadatos personalizados
 */

class Opinion_Section_Widget extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'opinion_section_widget',
            __('Sección de Opinión', 'textdomain'),
            array('description' => __('Muestra automáticamente los últimos 2 posts de opinión', 'textdomain'))
        );
    }

    /**
     * Frontend del widget
     */
    public function widget($args, $instance) {
        echo $args['before_widget'];

        if (!empty($instance['title'])) {
            echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
        }

        // Obtener los últimos 2 posts de opinión
        $opinion_posts = $this->get_latest_opinion_posts($instance);

        if (empty($opinion_posts)) {
            echo '<p>No hay posts de opinión disponibles.</p>';
            echo $args['after_widget'];
            return;
        }

        ?>
        <div class="opinion-section-widget">
            <div class="opinion-posts-container">
                
                <?php foreach($opinion_posts as $index => $post): 
                    $author_data = $this->get_author_metadata($post->ID, $instance, $index + 1);
                ?>
                <div class="opinion-post">
                    <div class="opinion-content">
                        <!-- Autor con imagen a la izquierda -->
                        <div class="opinion-author">
                            <?php if ($author_data['author_image']): ?>
                            <div class="author-image">
                                <a href="<?php echo get_permalink($post->ID); ?>">
                                    <img src="<?php echo esc_url($author_data['author_image']); ?>" 
                                         alt="<?php echo esc_attr($author_data['author_name']); ?>">
                                </a>
                            </div>
                            <?php endif; ?>
                            
                            <div class="author-info">
                                <div class="author-name"><?php echo esc_html($author_data['author_name']); ?></div>
                                <div class="author-position"><?php echo esc_html($author_data['author_position']); ?></div>
                            </div>
                        </div>
                        
                        <!-- Línea de separación -->
                        <hr class="author-separator">
                        
                        <!-- Frase destacada con enlace -->
                        <?php if ($author_data['featured_line']): ?>
                        <div class="opinion-featured-line">
                            <a href="<?php echo get_permalink($post->ID); ?>">
                                "<?php echo esc_html($author_data['featured_line']); ?>"
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
                
            </div>
        </div>

        <style>
        .opinion-section-widget {
            margin: 2em 0;
        }

        .opinion-posts-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin-top: 1.5rem;
        }

        .opinion-post {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 1.5rem;
            border-left: 4px solid #0073aa;
        }

        .opinion-title {
            margin: 0 0 1rem 0;
            font-size: 1.2rem;
            line-height: 1.4;
        }

        .opinion-title a {
            color: #333;
            text-decoration: none;
            font-weight: 600;
        }

        .opinion-title a:hover {
            color: #0073aa;
        }

        .opinion-featured-line {
            font-style: italic;
            color: #666;
            font-size: 1.1rem;
            margin-bottom: 1rem;
            border-left: 3px solid #0073aa;
            padding-left: 1rem;
            background: #e8f4f8;
            padding: 0.8rem 1rem;
            border-radius: 4px;
        }

        .opinion-featured-line a {
            color: inherit;
            text-decoration: none;
            display: block;
        }

        .opinion-featured-line a:hover {
            color: #0073aa;
        }

        .opinion-excerpt {
            color: #555;
            line-height: 1.6;
            margin-bottom: 1.5rem;
        }

        .opinion-author {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .author-image {
            flex-shrink: 0;
        }

        .author-image img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
        }

        .author-info {
            display: flex;
            flex-direction: column;
        }

        .author-name {
            font-weight: 600;
            color: #333;
            margin-bottom: 2px;
        }

        .author-position {
            font-size: 0.9rem;
            color: #666;
        }

        .author-separator {
            border: none;
            height: 1px;
            background: #ddd;
            margin: 1rem 0;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .opinion-posts-container {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }
        }
        </style>

        <?php
        echo $args['after_widget'];
    }

    /**
     * Obtener los últimos 2 posts de opinión
     */
    private function get_latest_opinion_posts($instance) {
        $args = array(
            'post_type' => 'post',
            'posts_per_page' => 2,
            'post_status' => 'publish',
            'orderby' => 'date',
            'order' => 'DESC'
        );

        // Si se especifica una categoría, filtrar por ella
        if (!empty($instance['opinion_category'])) {
            if (is_numeric($instance['opinion_category'])) {
                $args['cat'] = $instance['opinion_category'];
            } else {
                $args['category_name'] = $instance['opinion_category'];
            }
        }

        // Si se especifica un tag, filtrar por él
        if (!empty($instance['opinion_tag'])) {
            $args['tag'] = $instance['opinion_tag'];
        }

        return get_posts($args);
    }

    /**
     * Obtener metadatos del autor para un post específico
     */
    private function get_author_metadata($post_id, $instance, $author_number) {
        // Obtener datos directamente de los custom fields del post
        $opinion_quote = get_post_meta($post_id, '_mh_opinion_quote', true);
        $custom_author_name = get_post_meta($post_id, '_mh_custom_author_name', true);
        $author_title = get_post_meta($post_id, '_mh_author_title', true);
        $author_photo_fid = get_post_meta($post_id, '_mh_author_photo_fid', true);
        
        // Si hay FID de foto, obtener la URL
        $author_image_url = '';
        if (!empty($author_photo_fid)) {
            $author_image_url = wp_get_attachment_image_url($author_photo_fid, 'thumbnail');
        }
        
        // Fallbacks si no hay datos personalizados
        $fallback_author = get_the_author_meta('display_name', get_post_field('post_author', $post_id));
        $fallback_image = get_avatar_url(get_post_field('post_author', $post_id));
        
        return array(
            'author_name' => !empty($custom_author_name) ? $custom_author_name : $fallback_author,
            'author_position' => $author_title ?? '',
            'author_image' => !empty($author_image_url) ? $author_image_url : $fallback_image,
            'featured_line' => $opinion_quote ?? ''
        );
    }

    /**
     * Formulario de administración del widget
     */
    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : __('Opinión', 'textdomain');
        $opinion_category = !empty($instance['opinion_category']) ? $instance['opinion_category'] : '';
        $opinion_tag = !empty($instance['opinion_tag']) ? $instance['opinion_tag'] : '';
        ?>
        
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php _e('Título:'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" 
                   name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" 
                   value="<?php echo esc_attr($title); ?>">
        </p>

        <p>
            <label for="<?php echo esc_attr($this->get_field_id('opinion_category')); ?>">Categoría de Opinión:</label>
            <select class="widefat" id="<?php echo esc_attr($this->get_field_id('opinion_category')); ?>" 
                    name="<?php echo esc_attr($this->get_field_name('opinion_category')); ?>">
                <option value="">-- Todas las categorías --</option>
                <?php
                $categories = get_categories();
                foreach($categories as $category) {
                    $selected = ($opinion_category == $category->term_id) ? 'selected="selected"' : '';
                    echo '<option value="' . $category->term_id . '" ' . $selected . '>' . $category->name . '</option>';
                }
                ?>
            </select>
            <small>Selecciona la categoría "Opinión" para filtrar solo esos posts</small>
        </p>

        <p>
            <label for="<?php echo esc_attr($this->get_field_id('opinion_tag')); ?>">Tag de Opinión (opcional):</label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('opinion_tag')); ?>" 
                   name="<?php echo esc_attr($this->get_field_name('opinion_tag')); ?>" type="text" 
                   value="<?php echo esc_attr($opinion_tag); ?>"
                   placeholder="opinion, editorial, etc.">
            <small>Opcional: tag adicional para filtrar posts</small>
        </p>

        <hr style="margin: 20px 0;">
        <p style="background: #e7f3ff; padding: 10px; border-radius: 4px;">
            <strong>✅ Metadatos automáticos:</strong><br>
            Este widget lee automáticamente los metadatos de cada post:<br>
            • <strong>Frase destacada:</strong> _mh_opinion_quote<br>
            • <strong>Nombre autor:</strong> _mh_custom_author_name<br>
            • <strong>Cargo autor:</strong> _mh_author_title<br>
            • <strong>Foto autor:</strong> _mh_author_photo_fid<br><br>
            <strong>Funcionamiento:</strong><br>
            1. Crea posts y asígnalos a la categoría "Opinión"<br>
            2. Completa los custom fields arriba mencionados<br>
            3. El widget mostrará automáticamente los 2 más recientes<br>
            4. Si no hay custom fields, usará datos del autor del post
        </p>
        <?php
    }

    /**
     * Actualizar configuración del widget
     */
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? sanitize_text_field($new_instance['title']) : '';
        $instance['opinion_category'] = (!empty($new_instance['opinion_category'])) ? sanitize_text_field($new_instance['opinion_category']) : '';
        $instance['opinion_tag'] = (!empty($new_instance['opinion_tag'])) ? sanitize_text_field($new_instance['opinion_tag']) : '';
        
        return $instance;
    }
}

// Registrar el widget
function register_opinion_section_widget() {
    register_widget('Opinion_Section_Widget');
}
add_action('widgets_init', 'register_opinion_section_widget');

// Shortcode simplificado para últimos 2 posts - Lee metadatos automáticamente
function opinion_section_shortcode($atts) {
    $atts = shortcode_atts(array(
        'category' => '',
        'tag' => ''
    ), $atts);

    // Obtener últimos 2 posts
    $args = array(
        'post_type' => 'post',
        'posts_per_page' => 2,
        'post_status' => 'publish',
        'orderby' => 'date',
        'order' => 'DESC'
    );

    if (!empty($atts['category'])) {
        if (is_numeric($atts['category'])) {
            $args['cat'] = $atts['category'];
        } else {
            $args['category_name'] = $atts['category'];
        }
    }

    if (!empty($atts['tag'])) {
        $args['tag'] = $atts['tag'];
    }

    $posts = get_posts($args);

    if (empty($posts)) {
        return '<p>No hay posts de opinión disponibles.</p>';
    }

    ob_start();
    ?>
    <div class="opinion-section-shortcode">
        <div class="opinion-posts-container">
            
            <?php foreach($posts as $post): 
                // Obtener metadatos de custom fields
                $opinion_quote = get_post_meta($post->ID, '_mh_opinion_quote', true);
                $custom_author_name = get_post_meta($post->ID, '_mh_custom_author_name', true);
                $author_title = get_post_meta($post->ID, '_mh_author_title', true);
                $author_photo_fid = get_post_meta($post->ID, '_mh_author_photo_fid', true);
                
                // Si hay FID de foto, obtener la URL
                $author_image_url = '';
                if (!empty($author_photo_fid)) {
                    $author_image_url = wp_get_attachment_image_url($author_photo_fid, 'thumbnail');
                }
                
                // Fallbacks
                $display_author = !empty($custom_author_name) ? $custom_author_name : get_the_author_meta('display_name', get_post_field('post_author', $post->ID));
                $display_image = !empty($author_image_url) ? $author_image_url : get_avatar_url(get_post_field('post_author', $post->ID));
            ?>
            <div class="opinion-post">
                <div class="opinion-content">
                    <!-- Autor con imagen a la izquierda -->
                    <div class="opinion-author">
                        <div class="author-image">
                            <a href="<?php echo get_permalink($post->ID); ?>">
                                <img src="<?php echo esc_url($display_image); ?>" 
                                     alt="<?php echo esc_attr($display_author); ?>">
                            </a>
                        </div>
                        
                        <div class="author-info">
                            <div class="author-name"><?php echo esc_html($display_author); ?></div>
                            <div class="author-position"><?php echo esc_html($author_title ?: 'Autor'); ?></div>
                        </div>
                    </div>
                    
                    <!-- Línea de separación -->
                    <hr class="author-separator">
                    
                    <!-- Frase destacada con enlace -->
                    <?php if (!empty($opinion_quote)): ?>
                    <div class="opinion-featured-line">
                        <a href="<?php echo get_permalink($post->ID); ?>">
                            "<?php echo esc_html($opinion_quote); ?>"
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
            
        </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('opinion_section', 'opinion_section_shortcode');
?>