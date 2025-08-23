<?php
/**
 * Template para mostrar el archivo de investigaciones
 * 
 * @author Gonzalo Bianchi
 * @package MH Magazine Theme
 * @since 1.0
 */

get_header(); 

// Query personalizada para investigaciones
global $wp_query;
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

// 1. Obtener la investigación más reciente para destacar (siempre la misma)
$investigacion_destacada_query = new WP_Query(array(
    'post_type' => 'investigaciones',
    'post_status' => 'publish',
    'posts_per_page' => 1,
    'orderby' => 'date',
    'order' => 'DESC'
));

$destacada_id = null;
if ($investigacion_destacada_query->have_posts()) {
    $investigacion_destacada_query->the_post();
    $destacada_id = get_the_ID();
    wp_reset_postdata();
}

// 2. Query para las investigaciones restantes (excluyendo la destacada) - paginadas de a 6
$investigaciones_restantes_query = new WP_Query(array(
    'post_type' => 'investigaciones',
    'post_status' => 'publish',
    'posts_per_page' => 6,
    'paged' => $paged,
    'post__not_in' => $destacada_id ? array($destacada_id) : array(),
    'orderby' => 'date',
    'order' => 'DESC'
));

// DEBUG INFO (remover después)
$debug_total_investigaciones = wp_count_posts('investigaciones')->publish;
$debug_posts_showing = $investigaciones_restantes_query->post_count;
$debug_found_posts = $investigaciones_restantes_query->found_posts;

// Debug adicional: ¿Qué está buscando exactamente WordPress?
global $wp_query_original;
$debug_wp_query_posts = isset($wp_query_original) ? $wp_query_original->post_count : 'N/A';
$debug_wp_query_found = isset($wp_query_original) ? $wp_query_original->found_posts : 'N/A';
$debug_wp_query_pages = isset($wp_query_original) ? $wp_query_original->max_num_pages : 'N/A';

// ¿WordPress cree que existe esta página?
$debug_is_404 = is_404();
$debug_is_archive = is_post_type_archive('investigaciones');

// Query SQL debug
$debug_sql = $investigaciones_restantes_query->request;
?>

<div class="investigaciones-archive-wrapper">
    
    <!-- Título principal -->
    <div class="investigaciones-title-section">
        <div class="container">
            <h1 class="investigaciones-main-title">Investigaciones</h1>
        </div>
    </div>

    <!-- Layout principal con sidebar -->
    <div class="container">
        <div class="investigaciones-content-wrapper">
            
            <!-- Área de contenido principal -->
            <div class="investigaciones-content-area">
                
                <?php if ($destacada_id || $investigaciones_restantes_query->have_posts()) : ?>
                    
                    <!-- Investigación Destacada (siempre la más reciente) -->
                    <?php if ($destacada_id) : 
                        $investigacion_destacada_query->rewind_posts();
                        $investigacion_destacada_query->the_post();
                    ?>
                        <article class="investigacion-destacada">
                            <?php if (has_post_thumbnail()) : ?>
                                <div class="investigacion-destacada-image">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php the_post_thumbnail('large', array('class' => 'investigacion-destacada-thumb')); ?>
                                    </a>
                                </div>
                            <?php endif; ?>
                            
                            <div class="investigacion-destacada-content">
                                <h2 class="investigacion-destacada-title">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h2>
                                
                                <div class="investigacion-destacada-meta">
                                    <span class="investigacion-destacada-date">
                                        <i class="fa fa-calendar"></i>
                                        <?php echo get_the_date('j F Y'); ?>
                                    </span>
                                    
                                    <?php 
                                    $categories = get_the_terms(get_the_ID(), 'category_investigacion');
                                    if ($categories && !is_wp_error($categories)) : ?>
                                        <span class="investigacion-destacada-category">
                                            <i class="fa fa-folder"></i>
                                            <?php echo esc_html($categories[0]->name); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                
                                <?php if (has_excerpt()) : ?>
                                    <div class="investigacion-destacada-excerpt">
                                        <?php the_excerpt(); ?>
                                    </div>
                                <?php else : ?>
                                    <div class="investigacion-destacada-excerpt">
                                        <?php echo wp_trim_words(get_the_content(), 30, '...'); ?>
                                    </div>
                                <?php endif; ?>
                                
                                <a href="<?php the_permalink(); ?>" class="investigacion-destacada-link">
                                    Leer más <i class="fa fa-arrow-right"></i>
                                </a>
                            </div>
                        </article>
                        <?php wp_reset_postdata(); ?>
                    <?php endif; ?>

                    <!-- Grid de investigaciones restantes (6 paginadas, sin incluir la destacada) -->
                    <?php if ($investigaciones_restantes_query->have_posts()) : ?>
                        <div class="investigaciones-grid-container">
                            <?php while ($investigaciones_restantes_query->have_posts()) : 
                                $investigaciones_restantes_query->the_post(); 
                            ?>
                                <article class="investigacion-grid-item">
                                    <?php if (has_post_thumbnail()) : ?>
                                        <div class="investigacion-grid-image">
                                            <a href="<?php the_permalink(); ?>">
                                                <?php the_post_thumbnail('medium', array('class' => 'investigacion-grid-thumb')); ?>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="investigacion-grid-content">
                                        <h3 class="investigacion-grid-title">
                                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                        </h3>
                                        
                                        <div class="investigacion-grid-meta">
                                            <span class="investigacion-grid-date">
                                                <?php echo get_the_date('j M Y'); ?>
                                            </span>
                                            
                                            <?php 
                                            $categories = get_the_terms(get_the_ID(), 'category_investigacion');
                                            if ($categories && !is_wp_error($categories)) : ?>
                                                <span class="investigacion-grid-category">
                                                    <?php echo esc_html($categories[0]->name); ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <?php if (has_excerpt()) : ?>
                                            <div class="investigacion-grid-excerpt">
                                                <?php echo wp_trim_words(get_the_excerpt(), 15, '...'); ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <!-- Enlace overlay completo -->
                                    <a href="<?php the_permalink(); ?>" class="investigacion-grid-link-overlay" aria-label="Leer investigación completa"></a>
                                </article>
                            <?php endwhile; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Paginación (solo para las investigaciones restantes) -->
                    <?php if ($investigaciones_restantes_query->max_num_pages > 1) : ?>
                        <div class="investigaciones-pagination">
                            <?php
                            // Obtener la URL base correcta del archive
                            $base_url = get_post_type_archive_link('investigaciones');
                            
                            echo paginate_links(array(
                                'base' => trailingslashit($base_url) . 'page/%#%/',
                                'format' => '',
                                'current' => max(1, get_query_var('paged')),
                                'total' => $investigaciones_restantes_query->max_num_pages,
                                'prev_text' => '← Anterior',
                                'next_text' => 'Siguiente →',
                                'type' => 'plain',
                                'end_size' => 2,
                                'mid_size' => 1,
                                'add_args' => false
                            ));
                            ?>
                        </div>
                    <?php endif; ?>
                    
                <?php else : ?>
                    
                    <div class="no-investigaciones-found">
                        <h2>No se encontraron investigaciones</h2>
                        <p>Aún no hay investigaciones publicadas.</p>
                        <a href="<?php echo home_url(); ?>" class="btn-home">Volver al inicio</a>
                    </div>
                    
                <?php endif; 
                wp_reset_postdata(); ?>
                
            </div>
            
            <!-- Sidebar -->
            <div class="investigaciones-sidebar-area">
                <?php get_sidebar(); ?>
            </div>
            
        </div>
    </div>
</div>

<style>
/* Layout principal - siguiendo estructura de archive-opinion */
.investigaciones-archive-wrapper {
    max-width: 100%;
    margin: 0;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

/* Título principal */
.investigaciones-title-section {
    background: #f8f9fa;
    padding: 40px 0 20px 0;
    border-bottom: 1px solid #eee;
}

.investigaciones-main-title {
    font-size: 3rem;
    color: #333;
    margin: 0;
    font-weight: 700;
    text-align: center;
}

/* Layout de contenido con sidebar */
.investigaciones-content-wrapper {
    display: grid;
    grid-template-columns: 1fr 300px;
    gap: 30px;
    padding: 30px 0;
    min-height: 600px;
    align-items: start;
}

.investigaciones-content-area {
    min-width: 0;
    box-sizing: border-box;
    overflow-wrap: break-word;
}

/* Investigación destacada - como en el sitio original */
.investigacion-destacada {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    overflow: hidden;
    margin-bottom: 40px;
    transition: all 0.3s ease;
    border: 1px solid #e9ecef;
}

.investigacion-destacada:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 30px rgba(0,0,0,0.15);
}

.investigacion-destacada-image {
    height: 300px;
    overflow: hidden;
}

.investigacion-destacada-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.investigacion-destacada:hover .investigacion-destacada-image img {
    transform: scale(1.05);
}

.investigacion-destacada-content {
    padding: 30px;
}

.investigacion-destacada-title {
    font-size: 2rem;
    font-weight: 700;
    margin: 0 0 15px 0;
    line-height: 1.3;
}

.investigacion-destacada-title a {
    color: #333;
    text-decoration: none;
}

.investigacion-destacada-title a:hover {
    color: #0073aa;
}

.investigacion-destacada-meta {
    display: flex;
    gap: 20px;
    margin-bottom: 15px;
    font-size: 14px;
    color: #666;
    align-items: center;
}

.investigacion-destacada-meta i {
    margin-right: 5px;
    color: #0073aa;
}

.investigacion-destacada-excerpt {
    font-size: 16px;
    line-height: 1.6;
    color: #555;
    margin-bottom: 20px;
}

.investigacion-destacada-link {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: #0073aa;
    color: white;
    padding: 12px 20px;
    border-radius: 6px;
    text-decoration: none;
    font-weight: 600;
    transition: background 0.3s ease;
}

.investigacion-destacada-link:hover {
    background: #005a87;
    color: white;
}

/* Grid de investigaciones - 2 columnas para las restantes */
.investigaciones-grid-container {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 25px;
    margin-bottom: 40px;
}

/* Cards del grid */
.investigacion-grid-item {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 15px rgba(0,0,0,0.1);
    overflow: hidden;
    transition: all 0.3s ease;
    border: 1px solid #e9ecef;
    position: relative;
    cursor: pointer;
}

.investigacion-grid-item:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.investigacion-grid-image {
    height: 200px;
    overflow: hidden;
}

.investigacion-grid-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.investigacion-grid-item:hover .investigacion-grid-image img {
    transform: scale(1.05);
}

.investigacion-grid-content {
    padding: 20px;
}

.investigacion-grid-title {
    font-size: 1.2rem;
    font-weight: 600;
    margin: 0 0 10px 0;
    line-height: 1.4;
}

.investigacion-grid-title a {
    color: #333;
    text-decoration: none;
}

.investigacion-grid-title a:hover {
    color: #0073aa;
}

.investigacion-grid-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
    font-size: 13px;
    color: #666;
}

.investigacion-grid-date {
    font-weight: 500;
}

.investigacion-grid-category {
    background: #f8f9fa;
    padding: 3px 8px;
    border-radius: 12px;
    font-size: 12px;
    color: #0073aa;
    font-weight: 500;
}

.investigacion-grid-excerpt {
    font-size: 14px;
    line-height: 1.5;
    color: #666;
}

.investigacion-grid-link-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    text-decoration: none;
    z-index: 2;
}

/* Paginación - siguiendo estilo de archive-opinion */
.investigaciones-pagination {
    margin-top: 50px;
    text-align: center;
}

.investigaciones-pagination a,
.investigaciones-pagination span {
    display: inline-block;
    padding: 12px 18px;
    margin: 0 3px;
    background: #f8f9fa;
    color: #333;
    text-decoration: none;
    border-radius: 6px;
    border: 1px solid #e9ecef;
    font-weight: 500;
}

.investigaciones-pagination .current {
    background: #0073aa;
    color: white;
    border-color: #0073aa;
}

.investigaciones-pagination a:focus {
    outline: 2px solid #0073aa;
    outline-offset: 2px;
}

.investigaciones-pagination .dots {
    background: transparent;
    border: none;
    color: #999;
}

.investigaciones-pagination .prev,
.investigaciones-pagination .next {
    font-weight: 600;
}

/* Sin resultados */
.no-investigaciones-found {
    text-align: center;
    padding: 60px 20px;
    background: #f8f9fa;
    border-radius: 12px;
    margin: 40px 0;
}

.no-investigaciones-found h2 {
    color: #333;
    margin-bottom: 15px;
}

.no-investigaciones-found p {
    color: #666;
    margin-bottom: 25px;
}

.btn-home {
    background: #0073aa;
    color: white;
    padding: 12px 25px;
    text-decoration: none;
    border-radius: 6px;
    font-weight: 500;
    transition: background 0.3s ease;
}

.btn-home:hover {
    background: #005a87;
}

/* Sidebar */
.investigaciones-sidebar-area {
    box-sizing: border-box;
    width: 300px;
    max-width: 300px;
    min-width: 300px;
    background: #f8f9fa;
    padding: 20px;
    border-radius: 10px;
    border: 1px solid #e9ecef;
    position: sticky;
    top: 20px;
    max-height: calc(100vh - 40px);
    overflow-y: auto;
}

.investigaciones-sidebar-area .mh-sidebar {
    width: 100% !important;
    float: none !important;
    display: block !important;
    margin: 0 !important;
    padding: 0 !important;
    clear: none !important;
    position: static !important;
}

.investigaciones-sidebar-area .widget {
    margin-bottom: 25px !important;
    background: #fff !important;
    padding: 15px !important;
    border-radius: 8px !important;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05) !important;
}

.investigaciones-sidebar-area .widget:last-child {
    margin-bottom: 0 !important;
}

.investigaciones-sidebar-area .widget-title {
    margin: 0 0 15px 0 !important;
    font-size: 1.1rem !important;
    color: #0073aa !important;
    font-weight: 600 !important;
    border-bottom: 2px solid #0073aa !important;
    padding-bottom: 8px !important;
}

/* Responsive Design */
@media (max-width: 1024px) {
    .investigaciones-content-wrapper {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .investigaciones-sidebar-area {
        width: 100%;
        max-width: none;
        min-width: 0;
        position: static;
        max-height: none;
        overflow-y: visible;
        order: -1;
    }
    
    .investigaciones-grid-container {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .investigaciones-main-title {
        font-size: 2.5rem;
    }
    
    .investigacion-destacada-content {
        padding: 20px;
    }
    
    .investigacion-destacada-title {
        font-size: 1.6rem;
    }
    
    .investigacion-destacada-meta {
        flex-direction: column;
        gap: 8px;
        align-items: flex-start;
    }
}

@media (max-width: 480px) {
    .investigaciones-main-title {
        font-size: 2rem;
    }
    
    .investigacion-destacada-title {
        font-size: 1.4rem;
    }
}
</style>

<?php get_footer(); ?>