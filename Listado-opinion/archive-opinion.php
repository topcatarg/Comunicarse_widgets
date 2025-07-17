<?php
/**
 * Archive template for Opinion posts - Versión Simplificada
 * Listado de todas las opiniones con diseño de cards simple
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
                    
                    <article class="opinion-card-simple">
                        <div class="opinion-card-content">
                            
                            <!-- Foto del autor -->
                            <?php if ($author_photo_id): 
                                $photo = wp_get_attachment_image($author_photo_id, 'thumbnail', false, array('class' => 'author-photo-simple'));
                                if ($photo): ?>
                                    <div class="author-photo-container">
                                        <?php echo $photo; ?>
                                    </div>
                                <?php endif;
                            endif; ?>
                            
                            <!-- Nombre del autor -->
                            <div class="author-name-simple">
                                <?php echo esc_html($author_name); ?>
                                <?php if ($author_title): ?>
                                    <span class="author-title-simple"><?php echo esc_html($author_title); ?></span>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Comentario destacado -->
                            <?php if ($opinion_quote): ?>
                                <div class="opinion-quote-simple">
                                    <blockquote>"<?php echo esc_html($opinion_quote); ?>"</blockquote>
                                </div>
                            <?php endif; ?>
                            
                        </div>
                        
                        <!-- Enlace completo en toda la card -->
                        <a href="<?php the_permalink(); ?>" class="opinion-card-link-overlay" aria-label="Leer opinión completa"></a>
                    </article>
                    
                <?php endwhile; ?>
            </div>

            <!-- Paginación -->
            <div class="opinion-pagination">
                <?php
                if ($opinion_query->max_num_pages > 1) {
                    echo paginate_links(array(
                        'base' => str_replace(999999999, '%#%', esc_url(get_pagenum_link(999999999))),
                        'format' => '?paged=%#%',
                        'current' => max(1, get_query_var('paged')),
                        'total' => $opinion_query->max_num_pages,
                        'prev_text' => '← Anterior',
                        'next_text' => 'Siguiente →',
                        'type' => 'plain',
                        'end_size' => 2,
                        'mid_size' => 1,
                        'add_args' => false
                    ));
                }
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

/* Cards simplificadas */
.opinion-card-simple {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    overflow: hidden;
    transition: all 0.3s ease;
    border: 1px solid #e9ecef;
    position: relative;
    cursor: pointer;
    padding: 20px;
    text-align: center;
}

.opinion-card-simple:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.opinion-card-content {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 15px;
}

/* Foto del autor */
.author-photo-container {
    margin-bottom: 5px;
}

.author-photo-simple {
    width: 80px !important;
    height: 80px !important;
    border-radius: 50% !important;
    object-fit: cover !important;
    border: 3px solid #fff !important;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1) !important;
}

/* Nombre del autor */
.author-name-simple {
    color: #333;
    font-weight: 600;
    font-size: 1.1rem;
    line-height: 1.2;
}

.author-title-simple {
    display: block;
    font-size: 0.9rem;
    color: #666;
    font-weight: 400;
    font-style: italic;
    margin-top: 2px;
}

/* Comentario destacado */
.opinion-quote-simple {
    flex: 1;
    display: flex;
    align-items: center;
}

.opinion-quote-simple blockquote {
    margin: 0;
    font-style: italic;
    color: #0073aa;
    font-size: 1rem;
    line-height: 1.4;
    text-align: center;
    position: relative;
}

.opinion-quote-simple blockquote:before {
    content: '"';
    font-size: 3rem;
    color: #0073aa;
    opacity: 0.3;
    position: absolute;
    top: -10px;
    left: -20px;
}

/* Enlace overlay */
.opinion-card-link-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    text-decoration: none;
    z-index: 2;
}

/* Paginación SIMPLIFICADA - sin hover */
.opinion-pagination {
    margin-top: 50px;
    text-align: center;
}

.opinion-pagination a,
.opinion-pagination span {
    display: inline-block !important;
    padding: 12px 18px !important;
    margin: 0 3px !important;
    background: #f8f9fa !important;
    color: #333 !important;
    text-decoration: none !important;
    border-radius: 6px !important;
    border: 1px solid #e9ecef !important;
    font-weight: 500 !important;
}

/* Página actual */
.opinion-pagination .current {
    background: #0073aa !important;
    color: white !important;
    border-color: #0073aa !important;
}

/* SIN HOVER - solo focus para accesibilidad */
.opinion-pagination a:focus {
    outline: 2px solid #0073aa !important;
    outline-offset: 2px !important;
}

/* Elementos deshabilitados */
.opinion-pagination .dots {
    background: transparent !important;
    border: none !important;
    color: #999 !important;
}

/* Navegación anterior/siguiente */
.opinion-pagination .prev,
.opinion-pagination .next {
    font-weight: 600 !important;
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

/* Sidebar styles */
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
    
    .opinion-card-simple {
        padding: 15px;
    }
    
    .author-photo-simple {
        width: 60px !important;
        height: 60px !important;
    }
}

@media (max-width: 480px) {
    .opinion-list-title {
        font-size: 2rem;
    }
    
    .opinion-card-simple {
        padding: 15px;
    }
}
</style>

<?php get_footer(); ?>