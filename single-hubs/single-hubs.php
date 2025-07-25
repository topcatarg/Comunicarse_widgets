<?php
/**
 * Template para post individual de hubs
 * Archivo: single-hubs.php
 * Ubicación: wp-content/themes/mh-magazine/single-hubs.php
 */

get_header(); 

// Obtener datos del post actual
$post_id = get_the_ID();
$categories = get_hub_post_categories($post_id);
$main_category = $categories && !is_wp_error($categories) ? $categories[0] : null;

// Función para obtener configuración de la categoría principal
function get_hub_category_config($category_slug) {
    $configs = array(
        'hub-category-cop-cambio-climatico' => array(
            'color_primary' => '#2ecc71',
            'color_secondary' => '#27ae60',
            'icon' => '🌍',
            'gradient' => 'linear-gradient(135deg, #2ecc71 0%, #27ae60 50%, #16a085 100%)',
            'theme' => 'green'
        ),
        'hub-category-ddhh-y-empresa' => array(
            'color_primary' => '#e74c3c',
            'color_secondary' => '#c0392b',
            'icon' => '⚖️',
            'gradient' => 'linear-gradient(135deg, #e74c3c 0%, #c0392b 50%, #a93226 100%)',
            'theme' => 'red'
        ),
        'hub-category-movilidad-sostenible' => array(
            'color_primary' => '#3498db',
            'color_secondary' => '#2980b9',
            'icon' => '🚗',
            'gradient' => 'linear-gradient(135deg, #3498db 0%, #2980b9 50%, #1f618d 100%)',
            'theme' => 'blue'
        ),
        'hub-category-economia-regenerativa' => array(
            'color_primary' => '#9b59b6',
            'color_secondary' => '#8e44ad',
            'icon' => '♻️',
            'gradient' => 'linear-gradient(135deg, #9b59b6 0%, #8e44ad 50%, #7d3c98 100%)',
            'theme' => 'purple'
        ),
        'hub-category-negocios-inclusivos-y-sociales' => array(
            'color_primary' => '#f39c12',
            'color_secondary' => '#e67e22',
            'icon' => '🤝',
            'gradient' => 'linear-gradient(135deg, #f39c12 0%, #e67e22 50%, #d35400 100%)',
            'theme' => 'orange'
        ),
        'hub-category-packaging-sustentable' => array(
            'color_primary' => '#1abc9c',
            'color_secondary' => '#16a085',
            'icon' => '📦',
            'gradient' => 'linear-gradient(135deg, #1abc9c 0%, #16a085 50%, #138d75 100%)',
            'theme' => 'teal'
        )
    );
    
    $default_config = array(
        'color_primary' => '#667eea',
        'color_secondary' => '#764ba2',
        'icon' => '🏢',
        'gradient' => 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
        'theme' => 'default'
    );
    
    return isset($configs[$category_slug]) ? $configs[$category_slug] : $default_config;
}

$config = $main_category ? get_hub_category_config($main_category->slug) : get_hub_category_config('default');
?>

<div class="hub-single-wrapper theme-<?php echo $config['theme']; ?>">
    
    <?php while (have_posts()) : the_post(); ?>
        
        <!-- Header del artículo -->
        <header class="hub-article-header" style="background: <?php echo $config['gradient']; ?>;">
            <div class="header-content">
                
                <!-- Breadcrumb -->
                <nav class="hub-breadcrumb" aria-label="breadcrumb">
                    <a href="<?php echo home_url(); ?>">Inicio</a>
                    <span class="separator">›</span>
                    <a href="<?php echo get_hubs_archive_url(); ?>">Hubs</a>
                    <?php if ($main_category) : ?>
                        <span class="separator">›</span>
                        <a href="<?php echo get_hub_category_archive_url($main_category->slug); ?>">
                            <?php echo esc_html($main_category->name); ?>
                        </a>
                    <?php endif; ?>
                    <span class="separator">›</span>
                    <span class="current">Artículo</span>
                </nav>
                
                <!-- Categoría principal -->
                <?php if ($main_category) : ?>
                    <div class="article-category">
                        <span class="category-badge" style="background: rgba(255,255,255,0.2); color: white;">
                            <span class="category-icon"><?php echo $config['icon']; ?></span>
                            <?php echo esc_html($main_category->name); ?>
                        </span>
                    </div>
                <?php endif; ?>
                
                <!-- Volanta -->
                <?php if (has_hub_volanta()) : ?>
                    <div class="article-volanta">
                        <?php the_hub_volanta(); ?>
                    </div>
                <?php endif; ?>
                
                <!-- Título principal -->
                <h1 class="article-title">
                    <?php the_title(); ?>
                </h1>
                
                <!-- Excerpt/Bajada -->
                <?php if (has_excerpt()) : ?>
                    <div class="article-excerpt">
                        <?php the_excerpt(); ?>
                    </div>
                <?php endif; ?>
                
                <!-- Meta información -->
                <div class="article-meta">
                    <div class="meta-item">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                            <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H3zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
                        </svg>
                        <span>Por <a href="<?php echo get_author_posts_url(get_the_author_meta('ID')); ?>"><?php the_author(); ?></a></span>
                    </div>
                    <div class="meta-item">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                            <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z"/>
                        </svg>
                        <span><?php echo get_the_date('j \d\e F \d\e Y'); ?></span>
                    </div>
                    <div class="meta-item">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                            <path d="M2 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v12l-4-2-4 2V2z"/>
                        </svg>
                        <span><?php echo get_estimated_reading_time(); ?> min de lectura</span>
                    </div>
                </div>
                
            </div>
            
            <!-- Imagen destacada -->
            <?php if (has_post_thumbnail()) : ?>
                <div class="article-featured-image">
                    <?php the_post_thumbnail('full', array('class' => 'featured-img')); ?>
                </div>
            <?php endif; ?>
            
        </header>
        
        <!-- Contenido principal -->
        <div class="hub-article-content">
            <div class="content-wrapper">
                
                <!-- Contenido del artículo -->
                <main class="article-main">
                    
                    <!-- Herramientas sociales flotantes -->
                    <div class="social-share-sidebar">
                        <div class="share-buttons">
                            <a href="#" class="share-btn facebook" title="Compartir en Facebook" style="background: <?php echo $config['color_primary']; ?>;">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                </svg>
                            </a>
                            <a href="#" class="share-btn twitter" title="Compartir en Twitter" style="background: <?php echo $config['color_primary']; ?>;">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                                </svg>
                            </a>
                            <a href="#" class="share-btn linkedin" title="Compartir en LinkedIn" style="background: <?php echo $config['color_primary']; ?>;">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                                </svg>
                            </a>
                            <a href="#" class="share-btn whatsapp" title="Compartir por WhatsApp" style="background: <?php echo $config['color_primary']; ?>;">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.905 3.488"/>
                                </svg>
                            </a>
                        </div>
                        
                        <!-- Scroll progress -->
                        <div class="scroll-progress">
                            <div class="progress-circle">
                                <svg width="40" height="40" viewBox="0 0 40 40">
                                    <circle cx="20" cy="20" r="18" fill="none" stroke="#e0e0e0" stroke-width="2"/>
                                    <circle cx="20" cy="20" r="18" fill="none" stroke="<?php echo $config['color_primary']; ?>" stroke-width="2" 
                                            stroke-dasharray="113" stroke-dashoffset="113" class="progress-bar" 
                                            style="transition: stroke-dashoffset 0.3s;"/>
                                </svg>
                                <span class="progress-text">0%</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Contenido del artículo -->
                    <div class="article-content">
                        <?php the_content(); ?>
                    </div>
                    
                    <!-- Tags -->
                    <?php $hub_tags = get_hub_post_tags(); ?>
                    <?php if ($hub_tags && !is_wp_error($hub_tags)) : ?>
                        <div class="article-tags">
                            <h4>Etiquetas:</h4>
                            <div class="tags-list">
                                <?php foreach ($hub_tags as $tag) : ?>
                                    <a href="<?php echo get_hub_tag_archive_url($tag->slug); ?>" 
                                       class="tag-link" 
                                       style="background: <?php echo $config['color_primary']; ?>20; color: <?php echo $config['color_primary']; ?>;">
                                        #<?php echo esc_html($tag->name); ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Navegación entre posts -->
                    <div class="hub-navigation">
                        <?php 
                        $prev_post = get_previous_post();
                        $next_post = get_next_post();
                        ?>
                        
                        <?php if ($prev_post || $next_post) : ?>
                            <div class="post-navigation">
                                <?php if ($prev_post) : ?>
                                    <div class="nav-item prev-post">
                                        <span class="nav-label">Artículo anterior</span>
                                        <a href="<?php echo get_permalink($prev_post); ?>" class="nav-link">
                                            <svg width="16" height="16" viewBox="0 0 16 16" fill="<?php echo $config['color_primary']; ?>">
                                                <path d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z"/>
                                            </svg>
                                            <span class="nav-title"><?php echo get_the_title($prev_post); ?></span>
                                        </a>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($next_post) : ?>
                                    <div class="nav-item next-post">
                                        <span class="nav-label">Siguiente artículo</span>
                                        <a href="<?php echo get_permalink($next_post); ?>" class="nav-link">
                                            <span class="nav-title"><?php echo get_the_title($next_post); ?></span>
                                            <svg width="16" height="16" viewBox="0 0 16 16" fill="<?php echo $config['color_primary']; ?>">
                                                <path d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z"/>
                                            </svg>
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                </main>
                
                <!-- Sidebar del artículo -->
                <aside class="article-sidebar">
                    
                    <!-- Widget de información del autor -->
                    <div class="widget author-widget">
                        <h3 class="widget-title">Sobre el autor</h3>
                        <div class="author-info">
                            <div class="author-avatar">
                                <?php echo get_avatar(get_the_author_meta('ID'), 80); ?>
                            </div>
                            <div class="author-details">
                                <h4 class="author-name">
                                    <a href="<?php echo get_author_posts_url(get_the_author_meta('ID')); ?>">
                                        <?php the_author(); ?>
                                    </a>
                                </h4>
                                <p class="author-description">
                                    <?php 
                                    $author_description = get_the_author_meta('description');
                                    echo $author_description ? esc_html($author_description) : 'Autor especializado en temas de sustentabilidad y empresa.';
                                    ?>
                                </p>
                                <div class="author-stats">
                                    <span class="stat">
                                        <strong><?php echo count_user_posts(get_the_author_meta('ID'), 'hubs'); ?></strong>
                                        artículos de hubs
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Widget de hubs relacionados -->
                    <div class="widget related-hubs-widget">
                        <h3 class="widget-title">Artículos relacionados</h3>
                        <?php
                        $related_hubs = get_related_hubs($post_id, 4);
                        
                        if ($related_hubs) :
                        ?>
                            <div class="related-hubs-list">
                                <?php foreach ($related_hubs as $related_hub) : ?>
                                    <article class="related-hub-item">
                                        <?php if (has_post_thumbnail($related_hub->ID)) : ?>
                                            <div class="related-hub-thumb">
                                                <a href="<?php echo get_permalink($related_hub->ID); ?>">
                                                    <?php echo get_the_post_thumbnail($related_hub->ID, 'medium'); ?>
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <div class="related-hub-content">
                                            <h4 class="related-hub-title">
                                                <a href="<?php echo get_permalink($related_hub->ID); ?>">
                                                    <?php echo esc_html($related_hub->post_title); ?>
                                                </a>
                                            </h4>
                                            <div class="related-hub-meta">
                                                <span class="related-hub-date">
                                                    <?php echo get_the_date('j M', $related_hub->ID); ?>
                                                </span>
                                                <span class="related-hub-category">
                                                    <?php 
                                                    $related_categories = get_hub_post_categories($related_hub->ID);
                                                    if ($related_categories && !is_wp_error($related_categories)) {
                                                        echo esc_html($related_categories[0]->name);
                                                    }
                                                    ?>
                                                </span>
                                            </div>
                                        </div>
                                    </article>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Widget de volver a la categoría -->
                    <?php if ($main_category) : ?>
                        <div class="widget back-to-category-widget">
                            <div class="back-to-category" style="background: <?php echo $config['gradient']; ?>;">
                                <div class="category-info">
                                    <span class="category-icon"><?php echo $config['icon']; ?></span>
                                    <div class="category-text">
                                        <h4>Explora más sobre</h4>
                                        <h3><?php echo esc_html($main_category->name); ?></h3>
                                        <p><?php echo $main_category->count; ?> artículos disponibles</p>
                                    </div>
                                </div>
                                <a href="<?php echo get_hub_category_archive_url($main_category->slug); ?>" class="category-btn">
                                    Ver todos los artículos
                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                        <path d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8z"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Widget de publicidad lateral -->
                    <div class="widget sidebar-ad-widget">
                        <div class="sidebar-ad">
                            <span class="ad-label">Publicidad</span>
                            <div class="ad-banner-placeholder" style="background: <?php echo $config['color_primary']; ?>10;">
                                <div class="ad-content">
                                    <div class="ad-icon"><?php echo $config['icon']; ?></div>
                                    <h4>Banner lateral</h4>
                                    <p>300x600 o 300x250</p>
                                    <small><?php echo $main_category ? esc_html($main_category->name) : 'Publicidad'; ?></small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </aside>
                
            </div>
        </div>
        
    <?php endwhile; ?>
    
</div>

<?php
/**
 * Función helper para calcular tiempo de lectura estimado
 */
function get_estimated_reading_time() {
    $content = get_post_field('post_content', get_the_ID());
    $word_count = str_word_count(wp_strip_all_tags($content));
    $reading_time = ceil($word_count / 200); // 200 palabras por minuto promedio
    return max(1, $reading_time);
}
?>

<style>
/* Estilos para el template de post individual de hub */
.hub-single-wrapper {
    max-width: 100%;
    margin: 0;
    padding: 0;
}

/* Header del artículo */
.hub-article-header {
    position: relative;
    color: white;
    padding: 2rem 0 4rem 0;
    margin-bottom: 3rem;
    overflow: hidden;
}

.hub-article-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 20"><defs><radialGradient id="a"><stop offset="20%" stop-color="%23fff" stop-opacity="0.1"/><stop offset="100%" stop-color="%23fff" stop-opacity="0"/></radialGradient></defs><circle fill="url(%23a)" cx="50" cy="10" r="10"/></svg>') repeat;
    opacity: 0.1;
}

.header-content {
    max-width: 800px;
    margin: 0 auto;
    padding: 0 2rem;
    position: relative;
    z-index: 2;
    text-align: center;
}

/* Breadcrumb */
.hub-breadcrumb {
    margin-bottom: 2rem;
    font-size: 0.9rem;
    opacity: 0.9;
}

.hub-breadcrumb a {
    color: white;
    text-decoration: none;
    transition: opacity 0.3s;
}

.hub-breadcrumb a:hover {
    opacity: 0.8;
}

.hub-breadcrumb .separator {
    margin: 0 0.5rem;
    opacity: 0.7;
}

.hub-breadcrumb .current {
    opacity: 0.7;
}

/* Categoría */
.article-category {
    margin-bottom: 1rem;
}

.category-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    border-radius: 25px;
    font-size: 0.9rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.category-icon {
    font-size: 1.2rem;
}

/* Volanta */
.article-volanta {
    font-size: 1rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 1rem;
    opacity: 0.9;
}

/* Título */
.article-title {
    font-size: 3rem;
    font-weight: 800;
    line-height: 1.2;
    margin-bottom: 1.5rem;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
}

/* Excerpt */
.article-excerpt {
    font-size: 1.3rem;
    line-height: 1.6;
    margin-bottom: 2rem;
    opacity: 0.95;
    font-weight: 300;
}

/* Meta información */
.article-meta {
    display: flex;
    justify-content: center;
    gap: 2rem;
    font-size: 0.9rem;
    opacity: 0.9;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.meta-item a {
    color: white;
    text-decoration: none;
    transition: opacity 0.3s;
}

.meta-item a:hover {
    opacity: 0.8;
}

/* Imagen destacada */
.article-featured-image {
    margin-top: 3rem;
    text-align: center;
}

.featured-img {
    max-width: 100%;
    max-height: 400px;
    object-fit: cover;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.3);
}

/* Contenido principal */
.hub-article-content {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 2rem;
}

.content-wrapper {
    display: grid;
    grid-template-columns: 1fr 300px;
    gap: 3rem;
}

/* Sidebar social flotante */
.social-share-sidebar {
    position: fixed;
    left: 2rem;
    top: 50%;
    transform: translateY(-50%);
    z-index: 100;
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.share-buttons {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.share-btn {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    color: white;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}

.share-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.3);
}

/* Scroll progress */
.scroll-progress {
    position: relative;
}

.progress-circle {
    position: relative;
    width: 50px;
    height: 50px;
}

.progress-text {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-size: 0.8rem;
    font-weight: 600;
    color: #333;
}

/* Contenido del artículo */
.article-main {
    min-width: 0;
}

.article-content {
    font-size: 1.1rem;
    line-height: 1.8;
    color: #333;
    margin-bottom: 3rem;
}

.article-content h2,
.article-content h3,
.article-content h4 {
    margin-top: 2rem;
    margin-bottom: 1rem;
    color: #222;
}

.article-content h2 {
    font-size: 1.8rem;
    font-weight: 700;
}

.article-content h3 {
    font-size: 1.5rem;
    font-weight: 600;
}

.article-content h4 {
    font-size: 1.3rem;
    font-weight: 600;
}

.article-content p {
    margin-bottom: 1.5rem;
}

.article-content ul,
.article-content ol {
    margin-bottom: 1.5rem;
    padding-left: 2rem;
}

.article-content li {
    margin-bottom: 0.5rem;
}

.article-content blockquote {
    border-left: 4px solid #e0e0e0;
    padding-left: 2rem;
    margin: 2rem 0;
    font-style: italic;
    font-size: 1.2rem;
    color: #666;
}

.article-content img {
    max-width: 100%;
    height: auto;
    border-radius: 10px;
    margin: 2rem 0;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

/* Tags del artículo */
.article-tags {
    border-top: 2px solid #f0f0f0;
    padding-top: 2rem;
    margin-bottom: 3rem;
}

.article-tags h4 {
    margin-bottom: 1rem;
    color: #333;
    font-size: 1.1rem;
}

.tags-list {
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
}

.tags-list .tag-link {
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.9rem;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
}

.tags-list .tag-link:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}

/* Navegación entre posts */
.hub-navigation {
    border-top: 2px solid #f0f0f0;
    padding-top: 2rem;
    margin-bottom: 3rem;
}

.post-navigation {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
}

.nav-item {
    padding: 1.5rem;
    background: #f8f9fa;
    border-radius: 10px;
    transition: all 0.3s ease;
}

.nav-item:hover {
    background: #f0f0f0;
    transform: translateY(-2px);
}

.nav-label {
    display: block;
    font-size: 0.9rem;
    color: #666;
    margin-bottom: 0.5rem;
    text-transform: uppercase;
    font-weight: 600;
}

.nav-link {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    text-decoration: none;
    color: #333;
    font-weight: 600;
}

.nav-title {
    line-height: 1.3;
}

.next-post .nav-link {
    flex-direction: row-reverse;
    text-align: right;
}

/* Sidebar del artículo */
.article-sidebar {
    min-width: 0;
}

.widget {
    background: white;
    border-radius: 15px;
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    border: 1px solid #eee;
}

.widget-title {
    font-size: 1.3rem;
    margin-bottom: 1.5rem;
    color: #333;
    font-weight: 700;
}

/* Widget del autor */
.author-widget .author-info {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
}

.author-avatar {
    margin-bottom: 1rem;
}

.author-avatar img {
    border-radius: 50%;
    border: 4px solid #f0f0f0;
}

.author-name {
    margin-bottom: 0.5rem;
    font-size: 1.2rem;
}

.author-name a {
    color: #333;
    text-decoration: none;
}

.author-name a:hover {
    color: #0066cc;
}

.author-description {
    color: #666;
    line-height: 1.6;
    margin-bottom: 1rem;
}

.author-stats {
    font-size: 0.9rem;
    color: #888;
}

/* Widget de relacionados */
.related-hubs-list {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.related-hub-item {
    display: flex;
    gap: 1rem;
    padding-bottom: 1.5rem;
    border-bottom: 1px solid #eee;
}

.related-hub-item:last-child {
    border-bottom: none;
    padding-bottom: 0;
}

.related-hub-thumb {
    flex: 0 0 80px;
}

.related-hub-thumb img {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 8px;
}

.related-hub-content {
    flex: 1;
    min-width: 0;
}

.related-hub-title {
    margin: 0 0 0.5rem 0;
    font-size: 1rem;
    line-height: 1.3;
}

.related-hub-title a {
    color: #333;
    text-decoration: none;
}

.related-hub-title a:hover {
    color: #0066cc;
}

.related-hub-meta {
    font-size: 0.8rem;
    color: #888;
    display: flex;
    gap: 1rem;
}

/* Widget de volver a categoría */
.back-to-category {
    color: white;
    padding: 2rem;
    border-radius: 15px;
    text-align: center;
    position: relative;
    overflow: hidden;
}

.back-to-category::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 20"><defs><radialGradient id="a"><stop offset="20%" stop-color="%23fff" stop-opacity="0.1"/><stop offset="100%" stop-color="%23fff" stop-opacity="0"/></radialGradient></defs><circle fill="url(%23a)" cx="50" cy="10" r="10"/></svg>') repeat;
    opacity: 0.1;
}

.category-info {
    position: relative;
    z-index: 2;
    margin-bottom: 1.5rem;
}

.category-info .category-icon {
    font-size: 2rem;
    margin-bottom: 1rem;
    display: block;
}

.category-info h4 {
    margin: 0 0 0.5rem 0;
    font-size: 0.9rem;
    opacity: 0.9;
    text-transform: uppercase;
}

.category-info h3 {
    margin: 0 0 0.5rem 0;
    font-size: 1.3rem;
    font-weight: 700;
}

.category-info p {
    margin: 0;
    opacity: 0.8;
    font-size: 0.9rem;
}

.category-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: rgba(255,255,255,0.2);
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 25px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
    position: relative;
    z-index: 2;
}

.category-btn:hover {
    background: rgba(255,255,255,0.3);
    transform: translateY(-2px);
}

/* Widget de publicidad */
.sidebar-ad {
    text-align: center;
    position: relative;
}

.sidebar-ad .ad-label {
    position: absolute;
    top: -10px;
    left: 50%;
    transform: translateX(-50%);
    background: #fff;
    padding: 0 10px;
    font-size: 0.8rem;
    color: #666;
    text-transform: uppercase;
}

.sidebar-ad .ad-banner-placeholder {
    min-height: 300px;
    border: 2px dashed #ddd;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.sidebar-ad .ad-content {
    text-align: center;
}

.sidebar-ad .ad-icon {
    font-size: 2rem;
    margin-bottom: 1rem;
}

.sidebar-ad h4 {
    margin: 0 0 0.5rem 0;
    color: #333;
}

.sidebar-ad p {
    margin: 0 0 0.5rem 0;
    color: #666;
}

.sidebar-ad small {
    color: #999;
    font-size: 0.8rem;
}

/* Responsive */
@media (max-width: 1200px) {
    .social-share-sidebar {
        display: none;
    }
}

@media (max-width: 768px) {
    .content-wrapper {
        grid-template-columns: 1fr;
        gap: 2rem;
    }
    
    .hub-article-content {
        padding: 0 1rem;
    }
    
    .header-content {
        padding: 0 1rem;
    }
    
    .article-title {
        font-size: 2rem;
    }
    
    .article-excerpt {
        font-size: 1.1rem;
    }
    
    .article-meta {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .post-navigation {
        grid-template-columns: 1fr;
    }
    
    .related-hub-item {
        flex-direction: column;
        align-items: center;
        text-align: center;
    }
    
    .related-hub-thumb {
        flex: none;
    }
}

@media (max-width: 480px) {
    .hub-article-header {
        padding: 1rem 0 2rem 0;
    }
    
    .article-title {
        font-size: 1.6rem;
    }
    
    .article-excerpt {
        font-size: 1rem;
    }
    
    .article-content {
        font-size: 1rem;
    }
    
    .widget {
        padding: 1.5rem;
    }
}

/* Animaciones */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.article-content > * {
    animation: fadeInUp 0.6s ease-out;
}

/* Estilos específicos por tema */
.theme-green .article-content a {
    color: #2ecc71;
}

.theme-red .article-content a {
    color: #e74c3c;
}

.theme-blue .article-content a {
    color: #3498db;
}

.theme-purple .article-content a {
    color: #9b59b6;
}

.theme-orange .article-content a {
    color: #f39c12;
}

.theme-teal .article-content a {
    color: #1abc9c;
}

.theme-default .article-content a {
    color: #667eea;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // Scroll progress indicator
    const progressBar = document.querySelector('.progress-bar');
    const progressText = document.querySelector('.progress-text');
    
    if (progressBar && progressText) {
        function updateScrollProgress() {
            const scrollTop = window.pageYOffset;
            const docHeight = document.body.scrollHeight - window.innerHeight;
            const scrollPercent = (scrollTop / docHeight) * 100;
            
            const circumference = 2 * Math.PI * 18; // radio = 18
            const strokeDashoffset = circumference - (scrollPercent / 100) * circumference;
            
            progressBar.style.strokeDashoffset = strokeDashoffset;
            progressText.textContent = Math.round(scrollPercent) + '%';
        }
        
        window.addEventListener('scroll', updateScrollProgress);
        updateScrollProgress(); // Initial call
    }
    
    // Smooth scroll para enlaces internos
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    // Social share functionality
    const shareButtons = document.querySelectorAll('.share-btn');
    shareButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const url = encodeURIComponent(window.location.href);
            const title = encodeURIComponent(document.title);
            const platform = this.classList[1]; // facebook, twitter, etc.
            
            let shareUrl = '';
            
            switch(platform) {
                case 'facebook':
                    shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${url}`;
                    break;
                case 'twitter':
                    shareUrl = `https://twitter.com/intent/tweet?url=${url}&text=${title}`;
                    break;
                case 'linkedin':
                    shareUrl = `https://www.linkedin.com/sharing/share-offsite/?url=${url}`;
                    break;
                case 'whatsapp':
                    shareUrl = `https://wa.me/?text=${title}%20${url}`;
                    break;
            }
            
            if (shareUrl) {
                window.open(shareUrl, '_blank', 'width=600,height=400,scrollbars=yes,resizable=yes');
            }
        });
    });
    
    // Lazy loading para imágenes
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.style.opacity = '0';
                    img.style.transition = 'opacity 0.3s';
                    
                    setTimeout(() => {
                        img.style.opacity = '1';
                    }, 100);
                    
                    observer.unobserve(img);
                }
            });
        });
        
        document.querySelectorAll('.article-content img').forEach(img => {
            imageObserver.observe(img);
        });
    }
    
});
</script>

<?php get_footer(); ?>