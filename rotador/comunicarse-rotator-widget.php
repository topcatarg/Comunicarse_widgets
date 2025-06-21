<?php
/**
 * Plugin Name: Rotador de Publicaciones Widget
 * Description: Widget que muestra un rotador de publicaciones con imagen destacada, título, excerpt y categoría
 * Version: 1.0
 * Author: Tu Nombre
 */

// Prevenir acceso directo
if (!defined('ABSPATH')) {
    exit;
}

class ComunicarseRotatorWidget extends WP_Widget {
    
    public function __construct() {
        parent::__construct(
            'comunicarse_rotator_widget',
            'Rotador de Publicaciones',
            array(
                'description' => 'Muestra un rotador con 4 publicaciones de una categoría específica'
            )
        );
        
        // Agregar CSS y JS inline cuando se use el widget
        add_action('wp_footer', array($this, 'output_inline_styles_and_scripts'));
    }
    
    private static $widget_used = false;
    
    // Frontend del widget
    public function widget($args, $instance) {
        self::$widget_used = true; // Marcar que el widget se está usando
        
        $title = apply_filters('widget_title', $instance['title']);
        $category_id = !empty($instance['category']) ? $instance['category'] : 1;
        $num_posts = !empty($instance['num_posts']) ? $instance['num_posts'] : 4;
        $rotation_speed = !empty($instance['rotation_speed']) ? $instance['rotation_speed'] : 5000;
        $show_excerpt = isset($instance['show_excerpt']) ? $instance['show_excerpt'] : true;
        $excerpt_length = !empty($instance['excerpt_length']) ? $instance['excerpt_length'] : 100;
        
        // Query para obtener los posts más recientes
        $posts_query = new WP_Query(array(
            'post_type' => 'post',
            'posts_per_page' => $num_posts,
            'cat' => $category_id,
            'post_status' => 'publish',
            'orderby' => 'date',
            'order' => 'DESC',
            'meta_query' => array(
                array(
                    'key' => '_thumbnail_id',
                    'compare' => 'EXISTS'
                )
            )
        ));
        
        if (!$posts_query->have_posts()) {
            return;
        }
        
        echo $args['before_widget'];
        
        if (!empty($title)) {
            echo $args['before_title'] . $title . $args['after_title'];
        }
        
        $widget_id = 'comunicarse-rotator-' . $this->number;
        ?>
        
        <div class="comunicarse-rotator-container" id="<?php echo $widget_id; ?>" data-speed="<?php echo $rotation_speed; ?>">
            <div class="rotator-main">
                <div class="rotator-featured">
                    <?php 
                    $post_index = 0;
                    while ($posts_query->have_posts()) : 
                        $posts_query->the_post();
                        $categories = get_the_category();
                        $primary_category = $this->get_display_category($categories, $category_id);
                        $active_class = $post_index === 0 ? ' active' : '';
                        ?>
                        <div class="featured-post<?php echo $active_class; ?>" data-index="<?php echo $post_index; ?>">
                            <div class="featured-image">
                                <?php if (has_post_thumbnail()) : ?>
                                    <a href="<?php the_permalink(); ?>">
                                        <?php the_post_thumbnail('large', array('class' => 'rotator-image')); ?>
                                    </a>
                                    <div class="image-overlay"></div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="featured-content">
                                <?php if ($primary_category) : ?>
                                    <span class="post-category" style="background-color: <?php echo $this->get_category_color($primary_category->term_id); ?>">
                                        <?php echo esc_html($primary_category->name); ?>
                                    </span>
                                <?php endif; ?>
                                
                                <h3 class="post-title">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h3>
                                
                                <?php if ($show_excerpt) : ?>
                                    <div class="post-excerpt">
                                        <?php echo $this->get_custom_excerpt($excerpt_length); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php 
                        $post_index++;
                    endwhile; 
                    wp_reset_postdata();
                    ?>
                </div>
                
                <div class="rotator-sidebar">
                    <ul class="rotator-list">
                        <?php 
                        $posts_query->rewind_posts();
                        $list_index = 0;
                        while ($posts_query->have_posts()) : 
                            $posts_query->the_post();
                            $categories = get_the_category();
                            $primary_category = $this->get_display_category($categories, $category_id);
                            $active_class = $list_index === 0 ? ' active' : '';
                            ?>
                            <li class="rotator-item<?php echo $active_class; ?>" data-index="<?php echo $list_index; ?>">
                                <div class="item-image">
                                    <?php if (has_post_thumbnail()) : ?>
                                        <?php the_post_thumbnail('thumbnail', array('class' => 'item-thumb')); ?>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="item-content">
                                    <?php if ($primary_category) : ?>
                                        <span class="item-category" style="background-color: <?php echo $this->get_category_color($primary_category->term_id); ?>">
                                            <?php echo esc_html($primary_category->name); ?>
                                        </span>
                                    <?php endif; ?>
                                    
                                    <h4 class="item-title">
                                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                    </h4>
                                </div>
                            </li>
                            <?php 
                            $list_index++;
                        endwhile; 
                        wp_reset_postdata();
                        ?>
                    </ul>
                    
                    <div class="rotator-controls">
                        <button class="rotator-prev" aria-label="Anterior">‹</button>
                        <div class="rotator-dots">
                            <?php for ($i = 0; $i < $num_posts; $i++) : ?>
                                <button class="rotator-dot<?php echo $i === 0 ? ' active' : ''; ?>" 
                                        data-index="<?php echo $i; ?>" 
                                        aria-label="Ir a post <?php echo $i + 1; ?>"></button>
                            <?php endfor; ?>
                        </div>
                        <button class="rotator-next" aria-label="Siguiente">›</button>
                    </div>
                </div>
            </div>
        </div>
        
        <?php
        echo $args['after_widget'];
    }
    
    // CSS y JS inline en el footer (solo si se usa el widget)
    public function output_inline_styles_and_scripts() {
        if (!self::$widget_used) {
            return;
        }
        ?>
        
        <style>
        .comunicarse-rotator-container {
            max-width: 100%;
            margin: 0 auto;
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .rotator-main {
            display: flex;
            flex-direction: column;
        }

        .rotator-featured {
            position: relative;
            height: 300px;
            overflow: hidden;
        }

        .featured-post {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            transition: opacity 0.5s ease-in-out;
        }

        .featured-post.active {
            opacity: 1;
        }

        .featured-image {
            position: relative;
            width: 100%;
            height: 100%;
        }

        .rotator-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .image-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 60%;
            background: linear-gradient(transparent, rgba(0,0,0,0.7));
        }

        .featured-content {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 20px;
            color: white;
            z-index: 10;
        }

        .post-category, .item-category {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            color: white;
            margin-bottom: 8px;
        }

        .post-title {
            margin: 0 0 10px 0;
            font-size: 22px;
            line-height: 1.3;
        }

        .post-title a {
            color: white;
            text-decoration: none;
        }

        .post-title a:hover {
            text-decoration: underline;
        }

        .post-excerpt {
            font-size: 14px;
            line-height: 1.4;
            margin-bottom: 10px;
            opacity: 0.9;
        }

        .rotator-sidebar {
            background: #f8f9fa;
            padding: 15px;
        }

        .rotator-list {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .rotator-item {
            display: flex;
            padding: 10px;
            margin-bottom: 10px;
            background: white;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
            opacity: 0.7;
        }

        .rotator-item.active {
            opacity: 1;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .rotator-item:hover {
            opacity: 1;
            transform: translateX(5px);
        }

        .item-image {
            flex-shrink: 0;
            width: 60px;
            height: 60px;
            margin-right: 10px;
        }

        .item-thumb {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 3px;
        }

        .item-content {
            flex: 1;
        }

        .item-category {
            font-size: 9px;
            padding: 2px 6px;
        }

        .item-title {
            margin: 5px 0;
            font-size: 13px;
            line-height: 1.3;
        }

        .item-title a {
            color: #333;
            text-decoration: none;
        }

        .item-title a:hover {
            color: #007cba;
        }

        .rotator-controls {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
        }

        .rotator-prev, .rotator-next {
            background: #007cba;
            color: white;
            border: none;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .rotator-prev:hover, .rotator-next:hover {
            background: #005a87;
        }

        .rotator-dots {
            display: flex;
            gap: 5px;
        }

        .rotator-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            border: none;
            background: #ccc;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .rotator-dot.active {
            background: #007cba;
        }

        @media (min-width: 768px) {
            .rotator-main {
                flex-direction: row;
            }
            
            .rotator-featured {
                flex: 2;
                height: 400px;
            }
            
            .rotator-sidebar {
                flex: 1;
                min-width: 300px;
            }
        }

        @media (max-width: 767px) {
            .featured-content {
                padding: 15px;
            }
            
            .post-title {
                font-size: 18px;
            }
            
            .rotator-item {
                padding: 8px;
            }
        }
        </style>
        
        <script>
        jQuery(document).ready(function($) {
            $(".comunicarse-rotator-container").each(function() {
                var $container = $(this);
                var $featuredPosts = $container.find(".featured-post");
                var $listItems = $container.find(".rotator-item");
                var $dots = $container.find(".rotator-dot");
                var $prevBtn = $container.find(".rotator-prev");
                var $nextBtn = $container.find(".rotator-next");
                
                var currentIndex = 0;
                var totalPosts = $featuredPosts.length;
                var rotationSpeed = parseInt($container.data("speed")) || 5000;
                var autoRotateInterval;
                
                function showPost(index) {
                    // Actualizar posts destacados
                    $featuredPosts.removeClass("active");
                    $featuredPosts.eq(index).addClass("active");
                    
                    // Actualizar lista lateral
                    $listItems.removeClass("active");
                    $listItems.eq(index).addClass("active");
                    
                    // Actualizar dots
                    $dots.removeClass("active");
                    $dots.eq(index).addClass("active");
                    
                    currentIndex = index;
                }
                
                function nextPost() {
                    var nextIndex = (currentIndex + 1) % totalPosts;
                    showPost(nextIndex);
                }
                
                function prevPost() {
                    var prevIndex = (currentIndex - 1 + totalPosts) % totalPosts;
                    showPost(prevIndex);
                }
                
                function startAutoRotate() {
                    stopAutoRotate();
                    autoRotateInterval = setInterval(nextPost, rotationSpeed);
                }
                
                function stopAutoRotate() {
                    if (autoRotateInterval) {
                        clearInterval(autoRotateInterval);
                    }
                }
                
                // Event listeners
                $listItems.on("click", function() {
                    var index = $(this).data("index");
                    showPost(index);
                    stopAutoRotate();
                    setTimeout(startAutoRotate, 2000);
                });
                
                $dots.on("click", function() {
                    var index = $(this).data("index");
                    showPost(index);
                    stopAutoRotate();
                    setTimeout(startAutoRotate, 2000);
                });
                
                $nextBtn.on("click", function() {
                    nextPost();
                    stopAutoRotate();
                    setTimeout(startAutoRotate, 2000);
                });
                
                $prevBtn.on("click", function() {
                    prevPost();
                    stopAutoRotate();
                    setTimeout(startAutoRotate, 2000);
                });
                
                // Pausar en hover
                $container.on("mouseenter", stopAutoRotate);
                $container.on("mouseleave", startAutoRotate);
                
                // Inicializar auto-rotación
                if (totalPosts > 1) {
                    startAutoRotate();
                }
                
                // Soporte para touch/swipe en móviles
                var startX = 0;
                var endX = 0;
                
                $container.on("touchstart", function(e) {
                    startX = e.originalEvent.touches[0].clientX;
                });
                
                $container.on("touchend", function(e) {
                    endX = e.originalEvent.changedTouches[0].clientX;
                    var diffX = startX - endX;
                    
                    if (Math.abs(diffX) > 50) {
                        if (diffX > 0) {
                            nextPost();
                        } else {
                            prevPost();
                        }
                        stopAutoRotate();
                        setTimeout(startAutoRotate, 2000);
                    }
                });
            });
        });
        </script>
        
        <?php
    }
    
    // Formulario de configuración en el admin
    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : 'Publicaciones Destacadas';
        $category = !empty($instance['category']) ? $instance['category'] : '';
        $num_posts = !empty($instance['num_posts']) ? $instance['num_posts'] : 4;
        $rotation_speed = !empty($instance['rotation_speed']) ? $instance['rotation_speed'] : 5000;
        $show_excerpt = isset($instance['show_excerpt']) ? $instance['show_excerpt'] : true;
        $excerpt_length = !empty($instance['excerpt_length']) ? $instance['excerpt_length'] : 100;
        ?>
        
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>">Título:</label>
            <input class="widefat" 
                   id="<?php echo $this->get_field_id('title'); ?>" 
                   name="<?php echo $this->get_field_name('title'); ?>" 
                   type="text" 
                   value="<?php echo esc_attr($title); ?>">
        </p>
        
        <p>
            <label for="<?php echo $this->get_field_id('category'); ?>">Categoría:</label>
            <select class="widefat" 
                    id="<?php echo $this->get_field_id('category'); ?>" 
                    name="<?php echo $this->get_field_name('category'); ?>">
                <option value="">Todas las categorías</option>
                <?php
                $categories = get_categories();
                foreach ($categories as $cat) {
                    $selected = selected($category, $cat->term_id, false);
                    echo "<option value='{$cat->term_id}' {$selected}>{$cat->name}</option>";
                }
                ?>
            </select>
        </p>
        
        <p>
            <label for="<?php echo $this->get_field_id('num_posts'); ?>">Número de posts:</label>
            <input class="widefat" 
                   id="<?php echo $this->get_field_id('num_posts'); ?>" 
                   name="<?php echo $this->get_field_name('num_posts'); ?>" 
                   type="number" 
                   min="2" 
                   max="10" 
                   value="<?php echo esc_attr($num_posts); ?>">
        </p>
        
        <p>
            <label for="<?php echo $this->get_field_id('rotation_speed'); ?>">Velocidad de rotación (ms):</label>
            <input class="widefat" 
                   id="<?php echo $this->get_field_id('rotation_speed'); ?>" 
                   name="<?php echo $this->get_field_name('rotation_speed'); ?>" 
                   type="number" 
                   min="1000" 
                   max="10000" 
                   step="500"
                   value="<?php echo esc_attr($rotation_speed); ?>">
        </p>
        
        <p>
            <input class="checkbox" 
                   type="checkbox" 
                   <?php checked($show_excerpt); ?> 
                   id="<?php echo $this->get_field_id('show_excerpt'); ?>" 
                   name="<?php echo $this->get_field_name('show_excerpt'); ?>" />
            <label for="<?php echo $this->get_field_id('show_excerpt'); ?>">Mostrar excerpt</label>
        </p>
        
        <p>
            <label for="<?php echo $this->get_field_id('excerpt_length'); ?>">Longitud del excerpt (caracteres):</label>
            <input class="widefat" 
                   id="<?php echo $this->get_field_id('excerpt_length'); ?>" 
                   name="<?php echo $this->get_field_name('excerpt_length'); ?>" 
                   type="number" 
                   min="50" 
                   max="300" 
                   value="<?php echo esc_attr($excerpt_length); ?>">
        </p>
        
        <?php
    }
    
    // Actualizar configuración
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        $instance['category'] = (!empty($new_instance['category'])) ? intval($new_instance['category']) : '';
        $instance['num_posts'] = (!empty($new_instance['num_posts'])) ? intval($new_instance['num_posts']) : 4;
        $instance['rotation_speed'] = (!empty($new_instance['rotation_speed'])) ? intval($new_instance['rotation_speed']) : 5000;
        $instance['show_excerpt'] = !empty($new_instance['show_excerpt']) ? 1 : 0;
        $instance['excerpt_length'] = (!empty($new_instance['excerpt_length'])) ? intval($new_instance['excerpt_length']) : 100;
        
        return $instance;
    }
    
    // Funciones auxiliares
    private function get_custom_excerpt($length = 100) {
        $excerpt = get_the_excerpt();
        if (strlen($excerpt) > $length) {
            $excerpt = substr($excerpt, 0, $length) . '...';
        }
        return $excerpt;
    }
    
    /**
     * Obtiene la categoría a mostrar (cualquiera excepto la seleccionada para filtrar)
     */
    private function get_display_category($categories, $selected_category_id) {
        if (empty($categories)) {
            return null;
        }
        
        // Si no hay categoría seleccionada (mostrar todas), usar la primera
        if (empty($selected_category_id)) {
            return $categories[0];
        }
        
        // Buscar una categoría que NO sea la seleccionada para filtrar
        foreach ($categories as $category) {
            if ($category->term_id != $selected_category_id) {
                return $category;
            }
        }
        
        // Si solo tiene la categoría seleccionada, no mostrar ninguna
        return null;
    }
    
    private function get_category_color($category_id) {
        // Generar color basado en el ID de la categoría
        $colors = array(
            '#e74c3c', '#3498db', '#2ecc71', '#f39c12', 
            '#9b59b6', '#1abc9c', '#34495e', '#e67e22',
            '#95a5a6', '#16a085', '#27ae60', '#2980b9'
        );
        
        return $colors[$category_id % count($colors)];
    }
}

// Registrar el widget
function register_comunicarse_rotator_widget() {
    register_widget('ComunicarseRotatorWidget');
}
add_action('widgets_init', 'register_comunicarse_rotator_widget');
?>