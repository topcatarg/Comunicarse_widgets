<?php
/**
 * Archive template for Opinion posts
 * Listado de todas las opiniones con diseño de cards
 * Author: Gonzalo Bianchi
 */

get_header(); ?>

<div class="opinion-main-wrapper-debug">
    <div class="opinion-content-area-debug">
        
        <!-- Header del listado -->
        <div class="opinion-list-header">
            <h1 class="opinion-list-title">Opinión</h1>
        </div>

        <?php
        // Configuración de la consulta
        $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
        
        $args = array(
            'post_type' => 'opinion',
            'posts_per_page' => 9,
            'post_status' => 'publish',
            'paged' => $paged,
            'orderby' => 'date',
            'order' => 'DESC'
        );

        $opinion_query = new WP_Query($args);
        
        if ($opinion_query->have_posts()) : ?>
            
            <div class="opinion-grid-container">
                <?php while ($opinion_query->have_posts()) : $opinion_query->the_post(); 
                    // Obtener metadatos
                    $author_name = get_post_meta(get_the_ID(), '_mh_custom_author_name', true);
                    $author_title = get_post_meta(get_the_ID(), '_mh_author_title', true);
                    $author_photo_id = get_post_meta(get_the_ID(), '_mh_author_photo_fid', true);
                    $opinion_quote = get_post_meta(get_the_ID(), '_mh_opinion_quote', true);
                    
                    // Fallbacks
                    if (empty($author_name)) {
                        $author_name = get_the_author();
                    }
                ?>
                    
                    <article class="opinion-card">
                        <div class="opinion-card-content">
                            
                            <!-- Título de la opinión -->
                            <h2 class="opinion-card-title">
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_title(); ?>
                                </a>
                            </h2>
                            
                            <!-- Información del autor -->
                            <div class="opinion-card-author">
                                <?php if ($author_photo_id): 
                                    $photo = wp_get_attachment_image($author_photo_id, 'thumbnail', false, array('class' => 'author-card-photo'));
                                    if ($photo): ?>
                                    <div class="author-card-photo-container">
                                        <?php echo $photo; ?>
                                    </div>
                                    <?php endif;
                                endif; ?>
                                
                                <div class="author-card-info">
                                    <div class="author-card-name"><?php echo esc_html($author_name); ?></div>
                                    <?php if ($author_title): ?>
                                        <div class="author-card-title"><?php echo esc_html($author_title); ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <!-- Cita de opinión -->
                            <?php if ($opinion_quote): ?>
                                <div class="opinion-card-quote">
                                    <blockquote>"<?php echo esc_html($opinion_quote); ?>"</blockquote>
                                </div>
                            <?php endif; ?>
                            
                        </div>
                        
                        <!-- Enlace completo en toda la card -->
                        <a href="<?php the_permalink(); ?>" class="opinion-card-overlay-link" aria-label="Leer opinión: <?php the_title(); ?>"></a>
                    </article>
                    
                <?php endwhile; ?>
            </div>

            <!-- Paginación -->
            <div class="opinion-pagination">
                <?php
                $big = 999999999; // Need an unlikely integer
                
                echo paginate_links(array(
                    'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
                    'format' => '?paged=%#%',
                    'current' => max(1, get_query_var('paged')),
                    'total' => $opinion_query->max_num_pages,
                    'prev_text' => '<i class="fas fa-chevron-left"></i> Anterior',
                    'next_text' => 'Siguiente <i class="fas fa-chevron-right"></i>',
                    'type' => 'list',
                    'end_size' => 3,
                    'mid_size' => 1
                ));
                ?>
            </div>
            
        <?php else : ?>
            
            <div class="no-opinions-found">
                <h2>No se encontraron opiniones</h2>
                <p>Aún no hay columnas de opinión publicadas.</p>
                <a href="<?php echo home_url(); ?>" class="btn-home">Volver al inicio</a>
            </div>
            
        <?php endif; 
        wp_reset_postdata(); ?>
        
    </div>
    
    <!-- Sidebar -->
    <div class="opinion-sidebar-area-debug">
        <?php get_sidebar(); ?>
    </div>
</div>

<style>
/* Layout principal - manteniendo estructura de single-opinion */
.opinion-main-wrapper-debug {
    display: grid !important;
    grid-template-columns: 1fr 300px !important;
    gap: 30px !important;
    max-width: 1200px !important;
    margin: 0 auto !important;
    padding: 20px !important;
    box-sizing: border-box !important;
    align-items: start !important;
    background: #fff !important;
}

.opinion-content-area-debug {
    min-width: 0 !important;
    box-sizing: border-box !important;
    overflow-wrap: break-word !important;
}

.opinion-sidebar-area-debug {
    box-sizing: border-box !important;
    width: 300px !important;
    max-width: 300px !important;
    min-width: 300px !important;
    background: #f8f9fa !important;
    padding: 20px !important;
    border-radius: 10px !important;
    border: 1px solid #e9ecef !important;
    position: sticky !important;
    top: 20px !important;
    max-height: calc(100vh - 40px) !important;
    overflow-y: auto !important;
}

/* Header del listado */
.opinion-list-header {
    text-align: center;
    margin-bottom: 40px;
    padding-bottom: 20px;
    border-bottom: 3px solid #0073aa;
}

.opinion-list-title {
    font-size: 3rem;
    color: #333;
    margin: 0;
    font-weight: 700;
}

/* Grid de opiniones - 3 columnas */
.opinion-grid-container {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 25px;
    margin-bottom: 40px;
}

/* Cards individuales */
.opinion-card {
    background: linear-gradient(135deg, #f8f9fa 0%, #fff 100%);
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    overflow: hidden;
    transition: all 0.3s ease;
    border-left: 4px solid #0073aa;
    position: relative;
    cursor: pointer;
}

.opinion-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.opinion-card-content {
    padding: 25px;
    display: flex;
    flex-direction: column;
    height: 100%;
}

/* Enlace overlay para toda la card */
.opinion-card-overlay-link {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    text-decoration: none;
    z-index: 2;
}

/* Título de la card */
.opinion-card-title {
    margin: 0 0 20px 0;
    font-size: 1.3rem;
    line-height: 1.3;
    min-height: 60px;
}

.opinion-card-title a {
    color: #333;
    text-decoration: none;
    font-weight: 600;
    transition: color 0.3s ease;
    position: relative;
    z-index: 1;
    pointer-events: none;
}

.opinion-card:hover .opinion-card-title a {
    color: #0073aa;
}

/* Autor en la card */
.opinion-card-author {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 15px;
    padding-bottom: 15px;
    border-bottom: 1px solid #eee;
}

.author-card-photo-container .author-card-photo {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #fff;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.author-card-info {
    flex: 1;
}

.author-card-name {
    font-weight: 600;
    color: #333;
    margin-bottom: 2px;
    font-size: 0.95rem;
}

.author-card-title {
    font-size: 0.85rem;
    color: #666;
    font-style: italic;
}

/* Cita en la card */
.opinion-card-quote {
    flex: 1;
}

.opinion-card-quote blockquote {
    margin: 0;
    font-style: italic;
    color: #0073aa;
    font-size: 0.95rem;
    line-height: 1.4;
    padding: 15px;
    background: rgba(0,115,170,0.05);
    border-left: 3px solid #0073aa;
    border-radius: 6px;
}

/* Paginación */
.opinion-pagination {
    margin-top: 50px;
    text-align: center;
}

.opinion-pagination .page-numbers {
    display: inline-flex;
    list-style: none;
    margin: 0;
    padding: 0;
    gap: 5px;
}

.opinion-pagination .page-numbers li {
    margin: 0;
}

.opinion-pagination .page-numbers a,
.opinion-pagination .page-numbers span {
    display: inline-block;
    padding: 10px 15px;
    background: #f8f9fa;
    color: #333;
    text-decoration: none;
    border-radius: 6px;
    transition: all 0.3s ease;
    border: 1px solid #e9ecef;
}

.opinion-pagination .page-numbers a:hover,
.opinion-pagination .page-numbers .current {
    background: #0073aa;
    color: white;
    border-color: #0073aa;
}

/* No results */
.no-opinions-found {
    text-align: center;
    padding: 60px 20px;
    background: #f8f9fa;
    border-radius: 12px;
    margin: 40px 0;
}

.no-opinions-found h2 {
    color: #333;
    margin-bottom: 15px;
}

.no-opinions-found p {
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

/* Sidebar styles - manteniendo compatibilidad */
.opinion-sidebar-area-debug .mh-sidebar {
    width: 100% !important;
    float: none !important;
    display: block !important;
    margin: 0 !important;
    padding: 0 !important;
    clear: none !important;
    position: static !important;
}

.opinion-sidebar-area-debug .widget {
    margin-bottom: 25px !important;
    background: #fff !important;
    padding: 15px !important;
    border-radius: 8px !important;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05) !important;
}

.opinion-sidebar-area-debug .widget:last-child {
    margin-bottom: 0 !important;
}

.opinion-sidebar-area-debug .widget-title {
    margin: 0 0 15px 0 !important;
    font-size: 1.1rem !important;
    color: #0073aa !important;
    font-weight: 600 !important;
    border-bottom: 2px solid #0073aa !important;
    padding-bottom: 8px !important;
}

/* Responsive Design */
@media (max-width: 1024px) {
    .opinion-grid-container {
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }
    
    .opinion-main-wrapper-debug {
        grid-template-columns: 1fr !important;
        gap: 20px !important;
        padding: 15px !important;
    }
    
    .opinion-sidebar-area-debug {
        width: 100% !important;
        max-width: none !important;
        min-width: 0 !important;
        position: static !important;
        max-height: none !important;
        overflow-y: visible !important;
        order: -1 !important;
    }
}

@media (max-width: 768px) {
    .opinion-grid-container {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .opinion-list-title {
        font-size: 2.5rem;
    }
    
    .opinion-card-content {
        padding: 20px;
    }
    
    .opinion-card-title {
        min-height: auto;
        margin-bottom: 15px;
    }
    
    .opinion-card-meta {
        flex-direction: column;
        gap: 10px;
        align-items: stretch;
    }
    
    .opinion-card-link {
        text-align: center;
        justify-content: center;
    }
}

@media (max-width: 480px) {
    .opinion-list-title {
        font-size: 2rem;
    }
    
    .opinion-card-content {
        padding: 15px;
    }
    
    .opinion-card-author {
        flex-direction: column;
        text-align: center;
        gap: 8px;
    }
}
</style>

<?php get_footer(); ?>