<?php
/**
 * Template para archivo de categorías de hubs
 * Archivo: archive-hubs-category.php
 * Ubicación: wp-content/themes/mh-magazine/archive-hubs-category.php
 */

get_header(); 

// Obtener la categoría actual
$current_category = get_queried_object();
$category_slug = $current_category->slug;

// Función para obtener configuración de publicidad por categoría
function get_category_config($category_slug) {
    $configs = array(
        'hub-category-cop-cambio-climatico' => array(
            'color_primary' => '#2ecc71',
            'color_secondary' => '#27ae60',
            'icon' => '🌍',
            'gradient' => 'linear-gradient(135deg, #2ecc71 0%, #27ae60 50%, #16a085 100%)',
            'ad_image' => get_template_directory_uri() . '/assets/ads/cop-banner.jpg',
            'ad_link' => 'https://ejemplo.com/cop-cambio-climatico',
            'ad_alt' => 'Publicidad COP Cambio Climático'
        ),
        'hub-category-ddhh-y-empresa' => array(
            'color_primary' => '#e74c3c',
            'color_secondary' => '#c0392b',
            'icon' => '⚖️',
            'gradient' => 'linear-gradient(135deg, #e74c3c 0%, #c0392b 50%, #a93226 100%)',
            'ad_image' => get_template_directory_uri() . '/assets/ads/ddhh-banner.jpg',
            'ad_link' => 'https://ejemplo.com/ddhh-empresa',
            'ad_alt' => 'Publicidad DD.HH. y Empresa'
        ),
        'hub-category-movilidad-sostenible' => array(
            'color_primary' => '#3498db',
            'color_secondary' => '#2980b9',
            'icon' => '🚗',
            'gradient' => 'linear-gradient(135deg, #3498db 0%, #2980b9 50%, #1f618d 100%)',
            'ad_image' => get_template_directory_uri() . '/assets/ads/movilidad-banner.jpg',
            'ad_link' => 'https://ejemplo.com/movilidad-sostenible',
            'ad_alt' => 'Publicidad Movilidad Sostenible'
        ),
        'hub-category-economia-regenerativa' => array(
            'color_primary' => '#9b59b6',
            'color_secondary' => '#8e44ad',
            'icon' => '♻️',
            'gradient' => 'linear-gradient(135deg, #9b59b6 0%, #8e44ad 50%, #7d3c98 100%)',
            'ad_image' => get_template_directory_uri() . '/assets/ads/economia-banner.jpg',
            'ad_link' => 'https://ejemplo.com/economia-regenerativa',
            'ad_alt' => 'Publicidad Economía Regenerativa'
        ),
        'hub-category-negocios-inclusivos-y-sociales' => array(
            'color_primary' => '#f39c12',
            'color_secondary' => '#e67e22',
            'icon' => '🤝',
            'gradient' => 'linear-gradient(135deg, #f39c12 0%, #e67e22 50%, #d35400 100%)',
            'ad_image' => get_template_directory_uri() . '/assets/ads/negocios-banner.jpg',
            'ad_link' => 'https://ejemplo.com/negocios-inclusivos',
            'ad_alt' => 'Publicidad Negocios Inclusivos'
        ),
        'hub-category-packaging-sustentable' => array(
            'color_primary' => '#1abc9c',
            'color_secondary' => '#16a085',
            'icon' => '📦',
            'gradient' => 'linear-gradient(135deg, #1abc9c 0%, #16a085 50%, #138d75 100%)',
            'ad_image' => get_template_directory_uri() . '/assets/ads/packaging-banner.jpg',
            'ad_link' => 'https://ejemplo.com/packaging-sustentable',
            'ad_alt' => 'Publicidad Packaging Sustentable'
        )
    );
    
    // Configuración por defecto
    $default_config = array(
        'color_primary' => '#667eea',
        'color_secondary' => '#764ba2',
        'icon' => '🏢',
        'gradient' => 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
        'ad_image' => get_template_directory_uri() . '/assets/ads/default-banner.jpg',
        'ad_link' => 'https://ejemplo.com/publicidad-general',
        'ad_alt' => 'Publicidad General'
    );
    
    return isset($configs[$category_slug]) ? $configs[$category_slug] : $default_config;
}

// Obtener configuración para esta categoría
$config = get_category_config($category_slug);
?>

<div class="mh-wrapper hub-category-wrapper">
    <div class="mh-main-content">
        <main class="mh-content">
            
            <!-- Header dinámico de la categoría -->
            <header class="hub-category-header" style="background: <?php echo $config['gradient']; ?>;">
                <div class="category-hero">
                    <div class="category-icon"><?php echo $config['icon']; ?></div>
                    <h1 class="category-title">
                        <span class="category-label">Hubs de</span>
                        <?php echo esc_html($current_category->name); ?>
                    </h1>
                    
                    <?php if ($current_category->description): ?>
                        <p class="category-description">
                            <?php echo esc_html($current_category->description); ?>
                        </p>
                    <?php endif; ?>
                    
                    <div class="category-stats">
                        <div class="stat-item">
                            <span class="stat-number"><?php echo $current_category->count; ?></span>
                            <span class="stat-label">Publicaciones</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number"><?php echo date('Y'); ?></span>
                            <span class="stat-label">Actualizado</span>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Breadcrumb -->
            <div class="hub-breadcrumb">
                <nav aria-label="breadcrumb">
                    <a href="<?php echo home_url(); ?>">Inicio</a>
                    <span class="separator">›</span>
                    <a href="<?php echo get_hubs_archive_url(); ?>">Hubs</a>
                    <span class="separator">›</span>
                    <span class="current"><?php echo esc_html($current_category->name); ?></span>
                </nav>
            </div>

            <!-- Filtros y herramientas -->
            <div class="hub-tools">
                <div class="tools-left">
                    <div class="results-info">
                        <span class="results-count">
                            <?php
                            global $wp_query;
                            echo $wp_query->found_posts;
                            ?> resultados
                        </span>
                    </div>
                </div>
                
                <div class="tools-right">
                    <div class="sort-dropdown">
                        <select id="hub-sort" onchange="sortHubs(this.value)">
                            <option value="date-desc">Más recientes</option>
                            <option value="date-asc">Más antiguos</option>
                            <option value="title-asc">A-Z</option>
                            <option value="views-desc">Más leídos</option>
                        </select>
                    </div>
                    
                    <div class="view-toggle">
                        <button class="view-btn active" data-view="grid" title="Vista de grilla" style="color: <?php echo $config['color_primary']; ?>;">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                <path d="M1 2.5A1.5 1.5 0 0 1 2.5 1h3A1.5 1.5 0 0 1 7 2.5v3A1.5 1.5 0 0 1 5.5 7h-3A1.5 1.5 0 0 1 1 5.5v-3zm8 0A1.5 1.5 0 0 1 10.5 1h3A1.5 1.5 0 0 1 15 2.5v3A1.5 1.5 0 0 1 13.5 7h-3A1.5 1.5 0 0 1 9 5.5v-3zm-8 8A1.5 1.5 0 0 1 2.5 9h3A1.5 1.5 0 0 1 7 10.5v3A1.5 1.5 0 0 1 5.5 15h-3A1.5 1.5 0 0 1 1 13.5v-3zm8 0A1.5 1.5 0 0 1 10.5 9h3a1.5 1.5 0 0 1 1.5 1.5v3a1.5 1.5 0 0 1-1.5 1.5h-3A1.5 1.5 0 0 1 9 13.5v-3z"/>
                            </svg>
                        </button>
                        <button class="view-btn" data-view="list" title="Vista de lista" style="color: <?php echo $config['color_primary']; ?>;">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                <path d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5z"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Publicidad específica de la categoría -->
            <div class="category-advertisement">
                <div class="ad-container">
                    <span class="ad-label">Publicidad</span>
                    <!-- Simulación de banner publicitario -->
                    <div class="ad-banner-placeholder" style="background: linear-gradient(45deg, <?php echo $config['color_primary']; ?>20, <?php echo $config['color_secondary']; ?>20);">
                        <div class="ad-content">
                            <div class="ad-icon"><?php echo $config['icon']; ?></div>
                            <h3>Publicidad especializada</h3>
                            <p>Espacio publicitario para <?php echo esc_html($current_category->name); ?></p>
                            <small>Banner 728x90 o 320x100 (responsive)</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lista de hubs -->
            <div class="hubs-container grid-view" id="hubs-container">
                <?php if (have_posts()) : ?>
                    
                    <?php 
                    $post_count = 0;
                    while (have_posts()) : the_post(); 
                        $post_count++;
                    ?>
                        <article class="hub-card" data-date="<?php echo get_the_date('Y-m-d'); ?>" data-title="<?php echo esc_attr(get_the_title()); ?>">
                            
                            <!-- Imagen destacada -->
                            <?php if (has_post_thumbnail()) : ?>
                                <div class="hub-image">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php the_post_thumbnail('large', array('class' => 'hub-thumb')); ?>
                                    </a>
                                    
                                    <!-- Badge de categoría -->
                                    <div class="hub-badge" style="background: <?php echo $config['color_primary']; ?>;">
                                        <?php echo $config['icon']; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Contenido -->
                            <div class="hub-content">
                                
                                <!-- Volanta -->
                                <?php if (has_hub_volanta()) : ?>
                                    <div class="hub-volanta" style="color: <?php echo $config['color_primary']; ?>;">
                                        <?php the_hub_volanta(); ?>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Título -->
                                <h2 class="hub-title">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php the_title(); ?>
                                    </a>
                                </h2>
                                
                                <!-- Excerpt -->
                                <?php if (has_excerpt()) : ?>
                                    <div class="hub-excerpt">
                                        <?php the_excerpt(); ?>
                                    </div>
                                <?php else : ?>
                                    <div class="hub-excerpt">
                                        <?php echo wp_trim_words(get_the_content(), 20, '...'); ?>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Meta información -->
                                <div class="hub-meta">
                                    <div class="meta-row">
                                        <span class="hub-author">
                                            <svg width="14" height="14" viewBox="0 0 16 16" fill="<?php echo $config['color_primary']; ?>">
                                                <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H3zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
                                            </svg>
                                            <a href="<?php echo get_author_posts_url(get_the_author_meta('ID')); ?>">
                                                <?php the_author(); ?>
                                            </a>
                                        </span>
                                        
                                        <span class="hub-date">
                                            <svg width="14" height="14" viewBox="0 0 16 16" fill="<?php echo $config['color_primary']; ?>">
                                                <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z"/>
                                            </svg>
                                            <?php echo get_the_date('j M, Y'); ?>
                                        </span>
                                    </div>
                                    
                                    <!-- Tags -->
                                    <?php $hub_tags = get_hub_post_tags(); ?>
                                    <?php if ($hub_tags && !is_wp_error($hub_tags)) : ?>
                                        <div class="hub-tags">
                                            <?php foreach (array_slice($hub_tags, 0, 3) as $tag) : ?>
                                                <a href="<?php echo get_hub_tag_archive_url($tag->slug); ?>" 
                                                   class="tag-link" 
                                                   style="background: <?php echo $config['color_primary']; ?>20; color: <?php echo $config['color_primary']; ?>;">
                                                    #<?php echo esc_html($tag->name); ?>
                                                </a>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Botón leer más -->
                                <div class="hub-read-more">
                                    <a href="<?php the_permalink(); ?>" 
                                       class="read-more-btn" 
                                       style="background: <?php echo $config['gradient']; ?>;">
                                        Leer artículo
                                        <svg width="14" height="14" viewBox="0 0 16 16" fill="currentColor">
                                            <path d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8z"/>
                                        </svg>
                                    </a>
                                </div>
                                
                            </div>
                        </article>

                        <?php 
                        // Insertar publicidad cada 6 posts
                        if ($post_count % 6 == 0 && $post_count < $wp_query->post_count) : 
                        ?>
                            <div class="inline-ad">
                                <div class="ad-container small">
                                    <span class="ad-label">Publicidad</span>
                                    <div class="ad-banner-placeholder small" style="background: <?php echo $config['color_primary']; ?>10;">
                                        <div class="ad-content">
                                            <small>Banner 300x250</small>
                                            <p><?php echo esc_html($current_category->name); ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                    <?php endwhile; ?>
                    
                <?php else : ?>
                    <div class="no-hubs-found">
                        <div class="no-content-icon"><?php echo $config['icon']; ?></div>
                        <h3>No hay contenido disponible</h3>
                        <p>Aún no se han publicado hubs en esta categoría. ¡Mantente atento a las próximas actualizaciones!</p>
                        <a href="<?php echo get_hubs_archive_url(); ?>" 
                           class="btn-primary" 
                           style="background: <?php echo $config['gradient']; ?>;">
                            Ver todos los hubs
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Paginación -->
            <?php if ($wp_query->max_num_pages > 1) : ?>
                <div class="hubs-pagination">
                    <?php
                    the_posts_pagination(array(
                        'mid_size' => 2,
                        'prev_text' => '<svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor"><path d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z"/></svg> Anterior',
                        'next_text' => 'Siguiente <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor"><path d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z"/></svg>',
                        'before_page_number' => '<span class="screen-reader-text">Página </span>',
                    ));
                    ?>
                </div>
            <?php endif; ?>
            
        </main>
    </div>

    <!-- Sidebar -->
    <?php if (is_active_sidebar('hub-sidebar') || true) : ?>
        <aside class="mh-sidebar hub-sidebar">
            
            <!-- Widget de navegación de categorías -->
            <div class="widget hub-categories-widget">
                <h3 class="widget-title">Categorías de Hubs</h3>
                <ul class="hub-categories-list">
                    <?php
                    $all_categories = get_hub_categories(array('hide_empty' => true));
                    foreach ($all_categories as $cat) :
                        $cat_config = get_category_config($cat->slug);
                        $is_current = ($cat->term_id === $current_category->term_id);
                    ?>
                        <li class="<?php echo $is_current ? 'current-category' : ''; ?>">
                            <a href="<?php echo get_hub_category_archive_url($cat->slug); ?>" 
                               style="<?php if ($is_current) echo 'background: ' . $cat_config['color_primary'] . '; color: white;'; ?>">
                                <span class="cat-icon"><?php echo $cat_config['icon']; ?></span>
                                <span class="cat-name"><?php echo esc_html($cat->name); ?></span>
                                <span class="cat-count"><?php echo $cat->count; ?></span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- Widget de hubs recientes de esta categoría -->
            <div class="widget recent-hubs-widget">
                <h3 class="widget-title">Últimos en <?php echo esc_html($current_category->name); ?></h3>
                <?php
                $recent_hubs = get_hubs_by_category($category_slug, array(
                    'posts_per_page' => 5,
                    'orderby' => 'date',
                    'order' => 'DESC'
                ));
                
                if ($recent_hubs) :
                ?>
                    <ul class="recent-hubs-list">
                        <?php foreach ($recent_hubs as $hub) : ?>
                            <li class="recent-hub-item">
                                <?php if (has_post_thumbnail($hub->ID)) : ?>
                                    <div class="recent-hub-thumb">
                                        <a href="<?php echo get_permalink($hub->ID); ?>">
                                            <?php echo get_the_post_thumbnail($hub->ID, 'thumbnail'); ?>
                                        </a>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="recent-hub-content">
                                    <h4 class="recent-hub-title">
                                        <a href="<?php echo get_permalink($hub->ID); ?>">
                                            <?php echo esc_html($hub->post_title); ?>
                                        </a>
                                    </h4>
                                    <span class="recent-hub-date">
                                        <?php echo get_the_date('j M', $hub->ID); ?>
                                    </span>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>

            <!-- Widget de estadísticas -->
            <div class="widget hub-stats-widget">
                <h3 class="widget-title">En números</h3>
                <?php $stats = get_hub_stats(); ?>
                <div class="stats-grid">
                    <div class="stat-item" style="border-left: 4px solid <?php echo $config['color_primary']; ?>;">
                        <span class="stat-number" style="color: <?php echo $config['color_primary']; ?>;">
                            <?php echo $current_category->count; ?>
                        </span>
                        <span class="stat-label">En esta categoría</span>
                    </div>
                    <div class="stat-item" style="border-left: 4px solid <?php echo $config['color_secondary']; ?>;">
                        <span class="stat-number" style="color: <?php echo $config['color_secondary']; ?>;">
                            <?php echo $stats['total_hubs']; ?>
                        </span>
                        <span class="stat-label">Total de hubs</span>
                    </div>
                    <div class="stat-item" style="border-left: 4px solid #95a5a6;">
                        <span class="stat-number" style="color: #95a5a6;">
                            <?php echo $stats['total_categories']; ?>
                        </span>
                        <span class="stat-label">Categorías</span>
                    </div>
                </div>
            </div>

        </aside>
    <?php endif; ?>
</div>

<style>
/* Estilos para el template de categorías de hubs */
.hub-category-wrapper {
    display: flex;
    gap: 2rem;
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1rem;
}

.mh-main-content {
    flex: 1;
    min-width: 0;
}

.hub-sidebar {
    flex: 0 0 300px;
}

/* Header de categoría */
.hub-category-header {
    color: white;
    padding: 3rem 0;
    margin-bottom: 2rem;
    border-radius: 15px;
    position: relative;
    overflow: hidden;
}

.hub-category-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 20"><defs><radialGradient id="a"><stop offset="20%" stop-color="%23fff" stop-opacity="0.1"/><stop offset="100%" stop-color="%23fff" stop-opacity="0"/></radialGradient></defs><circle fill="url(%23a)" cx="50" cy="10" r="10"/></svg>') repeat;
    opacity: 0.1;
}

.category-hero {
    text-align: center;
    max-width: 800px;
    margin: 0 auto;
    padding: 0 2rem;
    position: relative;
    z-index: 2;
}

.category-icon {
    font-size: 3rem;
    margin-bottom: 1rem;
    animation: float 3s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-10px); }
}

.category-title {
    font-size: 2.5rem;
    margin-bottom: 1rem;
    font-weight: 800;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
}

.category-label {
    display: block;
    font-size: 1rem;
    font-weight: 400;
    opacity: 0.9;
    margin-bottom: 0.5rem;
}

.category-description {
    font-size: 1.2rem;
    opacity: 0.95;
    margin-bottom: 1.5rem;
    line-height: 1.6;
}

.category-stats {
    display: flex;
    justify-content: center;
    gap: 2rem;
    margin-top: 1.5rem;
}

.category-stats .stat-item {
    text-align: center;
}

.category-stats .stat-number {
    display: block;
    font-size: 1.8rem;
    font-weight: bold;
    margin-bottom: 0.25rem;
}

.category-stats .stat-label {
    font-size: 0.9rem;
    opacity: 0.8;
}

/* Breadcrumb */
.hub-breadcrumb {
    margin-bottom: 1.5rem;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 8px;
}

.hub-breadcrumb nav {
    font-size: 0.9rem;
}

.hub-breadcrumb a {
    color: #0066cc;
    text-decoration: none;
}

.hub-breadcrumb a:hover {
    text-decoration: underline;
}

.hub-breadcrumb .separator {
    margin: 0 0.5rem;
    color: #666;
}

.hub-breadcrumb .current {
    color: #333;
    font-weight: 600;
}

/* Herramientas */
.hub-tools {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 10px;
}

.tools-left .results-count {
    font-weight: 600;
    color: #333;
}

.tools-right {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.sort-dropdown select {
    padding: 0.5rem;
    border: 1px solid #ddd;
    border-radius: 5px;
    background: white;
    font-size: 0.9rem;
}

.view-toggle {
    display: flex;
    gap: 0.25rem;
}

.view-btn {
    padding: 0.5rem;
    border: 1px solid #ddd;
    background: white;
    cursor: pointer;
    border-radius: 5px;
    transition: all 0.3s ease;
}

.view-btn.active {
    background: #f0f0f0;
    border-color: #ccc;
}

.view-btn:hover {
    background: #f5f5f5;
}

/* Publicidad */
.category-advertisement {
    margin: 2rem 0;
}

.ad-container {
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 10px;
    border: 2px dashed #ddd;
    position: relative;
    text-align: center;
}

.ad-container.small {
    margin: 1rem 0;
    max-width: 350px;
}

.ad-label {
    position: absolute;
    top: -10px;
    left: 20px;
    background: #fff;
    padding: 0 10px;
    font-size: 0.8rem;
    color: #666;
    text-transform: uppercase;
}

.ad-banner-placeholder {
    min-height: 120px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
}

.ad-banner-placeholder.small {
    min-height: 80px;
}

.ad-content {
    text-align: center;
}

.ad-content .ad-icon {
    font-size: 2rem;
    margin-bottom: 0.5rem;
}

.ad-content h3 {
    margin: 0 0 0.5rem 0;
    font-size: 1.1rem;
}

.ad-content p {
    margin: 0 0 0.5rem 0;
    color: #666;
}

.ad-content small {
    color: #999;
    font-size: 0.8rem;
}

/* Grid de hubs */
.hubs-container.grid-view {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 2rem;
    margin-bottom: 3rem;
}

.hubs-container.list-view {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.hubs-container.list-view .hub-card {
    display: flex;
    align-items: flex-start;
    gap: 1.5rem;
    max-width: 100%;
}

.hubs-container.list-view .hub-image {
    flex: 0 0 200px;
}

.hubs-container.list-view .hub-content {
    flex: 1;
}

/* Tarjetas de hub */
.hub-card {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    border: 1px solid #eee;
    position: relative;
}

.hub-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.hub-image {
    position: relative;
    overflow: hidden;
}

.hub-thumb {
    width: 100%;
    height: 220px;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.hub-card:hover .hub-thumb {
    transform: scale(1.05);
}

.hub-badge {
    position: absolute;
    top: 15px;
    right: 15px;
    color: white;
    padding: 0.5rem;
    border-radius: 50%;
    font-size: 1.2rem;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}

.hub-content {
    padding: 1.5rem;
}

.hub-volanta {
    background: rgba(255,255,255,0.9);
    padding: 0.5rem 1rem;
    border-radius: 20px;
    display: inline-block;
    font-size: 0.8rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 1rem;
}

.hub-title {
    margin: 0 0 1rem 0;
    font-size: 1.3rem;
    line-height: 1.4;
}

.hub-title a {
    color: #333;
    text-decoration: none;
    transition: color 0.3s ease;
}

.hub-title a:hover {
    color: #0066cc;
}

.hub-excerpt {
    color: #666;
    line-height: 1.6;
    margin-bottom: 1.5rem;
}

.hub-meta {
    border-top: 1px solid #eee;
    padding-top: 1rem;
    margin-bottom: 1rem;
}

.meta-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 0.75rem;
    font-size: 0.9rem;
}

.meta-row span {
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.meta-row a {
    color: #666;
    text-decoration: none;
}

.meta-row a:hover {
    color: #0066cc;
}

.hub-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.tag-link {
    padding: 0.25rem 0.75rem;
    border-radius: 15px;
    font-size: 0.75rem;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
}

.tag-link:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}

.read-more-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 25px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}

.read-more-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.3);
}

/* Publicidad inline */
.inline-ad {
    grid-column: 1 / -1;
    margin: 1rem 0;
}

/* Sin resultados */
.no-hubs-found {
    grid-column: 1 / -1;
    text-align: center;
    padding: 4rem 2rem;
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.05));
    border-radius: 20px;
    border: 2px dashed #ddd;
}

.no-content-icon {
    font-size: 4rem;
    margin-bottom: 1rem;
    animation: float 3s ease-in-out infinite;
}

.no-hubs-found h3 {
    margin-bottom: 1rem;
    color: #333;
}

.no-hubs-found p {
    color: #666;
    margin-bottom: 2rem;
}

.btn-primary {
    display: inline-block;
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 25px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.3);
}

/* Paginación */
.hubs-pagination {
    text-align: center;
    margin-top: 3rem;
}

.hubs-pagination .nav-links {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.hubs-pagination a,
.hubs-pagination span {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.75rem 1rem;
    border: 1px solid #ddd;
    border-radius: 8px;
    text-decoration: none;
    color: #666;
    transition: all 0.3s ease;
    min-width: 44px;
    justify-content: center;
}

.hubs-pagination a:hover,
.hubs-pagination .current {
    background: #0066cc;
    color: white;
    border-color: #0066cc;
}

.hubs-pagination svg {
    width: 14px;
    height: 14px;
}

/* Sidebar */
.widget {
    background: white;
    border-radius: 15px;
    padding: 1.5rem;
    margin-bottom: 2rem;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    border: 1px solid #eee;
}

.widget-title {
    font-size: 1.2rem;
    margin-bottom: 1rem;
    color: #333;
    border-bottom: 2px solid #0066cc;
    padding-bottom: 0.5rem;
}

/* Widget de categorías */
.hub-categories-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.hub-categories-list li {
    margin-bottom: 0.5rem;
}

.hub-categories-list a {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem;
    border-radius: 10px;
    text-decoration: none;
    color: #666;
    transition: all 0.3s ease;
    border: 1px solid transparent;
}

.hub-categories-list a:hover {
    background: #f8f9fa;
    border-color: #ddd;
}

.hub-categories-list .current-category a {
    border-color: transparent;
}

.cat-icon {
    font-size: 1.2rem;
    width: 24px;
    text-align: center;
}

.cat-name {
    flex: 1;
    font-weight: 500;
}

.cat-count {
    background: #f0f0f0;
    color: #666;
    padding: 0.25rem 0.5rem;
    border-radius: 12px;
    font-size: 0.8rem;
    font-weight: 600;
}

/* Widget de hubs recientes */
.recent-hubs-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.recent-hub-item {
    display: flex;
    gap: 1rem;
    margin-bottom: 1rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #eee;
}

.recent-hub-item:last-child {
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
}

.recent-hub-thumb {
    flex: 0 0 60px;
}

.recent-hub-thumb img {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: 8px;
}

.recent-hub-content {
    flex: 1;
    min-width: 0;
}

.recent-hub-title {
    margin: 0 0 0.5rem 0;
    font-size: 0.9rem;
    line-height: 1.3;
}

.recent-hub-title a {
    color: #333;
    text-decoration: none;
}

.recent-hub-title a:hover {
    color: #0066cc;
}

.recent-hub-date {
    font-size: 0.8rem;
    color: #888;
}

/* Widget de estadísticas */
.stats-grid {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.stats-grid .stat-item {
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 10px;
    position: relative;
    padding-left: 1.5rem;
}

.stats-grid .stat-number {
    display: block;
    font-size: 1.5rem;
    font-weight: bold;
    margin-bottom: 0.25rem;
}

.stats-grid .stat-label {
    font-size: 0.8rem;
    color: #666;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Responsive */
@media (max-width: 768px) {
    .hub-category-wrapper {
        flex-direction: column;
        gap: 1rem;
    }
    
    .hub-sidebar {
        flex: none;
    }
    
    .hubs-container.grid-view {
        grid-template-columns: 1fr;
    }
    
    .hub-tools {
        flex-direction: column;
        gap: 1rem;
        align-items: stretch;
    }
    
    .tools-right {
        justify-content: space-between;
    }
    
    .category-title {
        font-size: 2rem;
    }
    
    .category-stats {
        flex-direction: column;
        gap: 1rem;
    }
    
    .meta-row {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .hub-content {
        padding: 1rem;
    }
    
    .hubs-container.list-view .hub-card {
        flex-direction: column;
    }
    
    .hubs-container.list-view .hub-image {
        flex: none;
    }
}

@media (max-width: 480px) {
    .hub-category-wrapper {
        padding: 0 0.5rem;
    }
    
    .category-hero {
        padding: 0 1rem;
    }
    
    .hub-content {
        padding: 0.75rem;
    }
    
    .hubs-pagination .nav-links {
        gap: 0.25rem;
    }
    
    .hubs-pagination a,
    .hubs-pagination span {
        padding: 0.5rem 0.75rem;
        font-size: 0.9rem;
    }
}
</style>

<script>
// JavaScript para funcionalidades interactivas
document.addEventListener('DOMContentLoaded', function() {
    
    // Toggle vista grid/list
    const viewButtons = document.querySelectorAll('.view-btn');
    const hubsContainer = document.getElementById('hubs-container');
    
    viewButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const view = this.dataset.view;
            
            // Update active button
            viewButtons.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            // Update container class
            hubsContainer.className = `hubs-container ${view}-view`;
            
            // Guardar preferencia en localStorage
            localStorage.setItem('hubsViewPreference', view);
        });
    });
    
    // Restaurar preferencia de vista
    const savedView = localStorage.getItem('hubsViewPreference');
    if (savedView) {
        const targetBtn = document.querySelector(`[data-view="${savedView}"]`);
        if (targetBtn) {
            targetBtn.click();
        }
    }
    
});

// Función para ordenar hubs
function sortHubs(sortBy) {
    const container = document.getElementById('hubs-container');
    const cards = Array.from(container.querySelectorAll('.hub-card'));
    
    cards.sort((a, b) => {
        switch(sortBy) {
            case 'date-desc':
                return new Date(b.dataset.date) - new Date(a.dataset.date);
            case 'date-asc':
                return new Date(a.dataset.date) - new Date(b.dataset.date);
            case 'title-asc':
                return a.dataset.title.localeCompare(b.dataset.title);
            case 'views-desc':
                // Implementar cuando tengas datos de vistas
                return 0;
            default:
                return 0;
        }
    });
    
    // Reordenar elementos en el DOM
    cards.forEach(card => {
        if (!card.classList.contains('inline-ad')) {
            container.appendChild(card);
        }
    });
}

// Smooth scroll para anclas
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
</script>

<?php get_footer(); ?>