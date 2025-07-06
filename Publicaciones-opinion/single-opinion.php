<?php
/**
 * Template final para single-opinion.php
 * Basado en la estructura de debug que funciona perfectamente
 */

get_header(); ?>

<div class="opinion-main-wrapper-debug">
    
    <div class="opinion-content-area-debug">
        
        <!-- Contenido real de la opinión -->
        <?php while (have_posts()) : the_post(); 
            // Obtener metadatos de la opinión
            $opinion_quote = get_post_meta(get_the_ID(), '_mh_opinion_quote', true);
            $custom_author_name = get_post_meta(get_the_ID(), '_mh_custom_author_name', true);
            $author_title = get_post_meta(get_the_ID(), '_mh_author_title', true);
            $author_photo_fid = get_post_meta(get_the_ID(), '_mh_author_photo_fid', true);
            
            // Obtener foto del autor
            $author_photo = '';
            if ($author_photo_fid) {
                $author_photo = wp_get_attachment_image($author_photo_fid, 'medium', false, array('class' => 'author-photo-img'));
            }
        ?>
        
        <article id="post-<?php the_ID(); ?>" <?php post_class('opinion-article'); ?>>
            
            <!-- Título de la opinión -->
            <header class="opinion-header">
                <h1 class="opinion-title"><?php the_title(); ?></h1>
            </header>

            <!-- Recuadro gris con información del autor y frase destacada -->
            <div class="opinion-author-box">
                <div class="author-box-content">
                    
                    <!-- Foto destacada del post -->
                    <?php if (has_post_thumbnail()): ?>
                    <div class="opinion-featured-image">
                        <?php the_post_thumbnail('medium', array('class' => 'featured-img')); ?>
                    </div>
                    <?php endif; ?>

                    <!-- Información del autor -->
                    <div class="opinion-author-info">
                        <!-- Foto del autor -->
                        <?php if ($author_photo): ?>
                        <div class="author-photo">
                            <?php echo $author_photo; ?>
                        </div>
                        <?php endif; ?>

                        <!-- Datos del autor -->
                        <div class="author-details">
                            <?php if ($custom_author_name): ?>
                            <h3 class="author-name"><?php echo esc_html($custom_author_name); ?></h3>
                            <?php endif; ?>
                            
                            <?php if ($author_title): ?>
                            <p class="author-title"><?php echo esc_html($author_title); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Frase destacada de la opinión -->
                    <?php if ($opinion_quote): ?>
                    <div class="opinion-quote-highlight">
                        <blockquote class="opinion-quote">
                            "<?php echo esc_html($opinion_quote); ?>"
                        </blockquote>
                    </div>
                    <?php endif; ?>

                </div>
            </div>

            <!-- Contenido principal del post -->
            <div class="opinion-content">
                <?php
                the_content();
                
                wp_link_pages(array(
                    'before' => '<div class="page-links">' . esc_html__('Pages:', 'textdomain'),
                    'after'  => '</div>',
                ));
                ?>
            </div>

            <!-- Meta información adicional -->
            <footer class="opinion-meta">
                <div class="opinion-meta-info">
                    <span class="opinion-date">
                        <i class="fa fa-calendar"></i>
                        <?php echo get_the_date(); ?>
                    </span>
                    
                    <?php 
                    // Buscar categorías de opinion (taxonomía personalizada)
                    $opinion_categories = get_the_terms(get_the_ID(), 'categoria_opinion');
                    if ($opinion_categories && !is_wp_error($opinion_categories)): ?>
                    <span class="opinion-categories">
                        <i class="fa fa-folder"></i>
                        <?php 
                        $cat_links = array();
                        foreach ($opinion_categories as $category) {
                            $cat_links[] = '<a href="' . get_term_link($category) . '">' . esc_html($category->name) . '</a>';
                        }
                        echo implode(', ', $cat_links);
                        ?>
                    </span>
                    <?php 
                    // Si no hay categorías de opinion, buscar categorías normales
                    elseif (has_category()): ?>
                    <span class="opinion-categories">
                        <i class="fa fa-folder"></i>
                        <?php the_category(', '); ?>
                    </span>
                    <?php endif; ?>
                </div>
                
                <?php 
                // Buscar tags de opinion (taxonomía personalizada)
                $opinion_tags = get_the_terms(get_the_ID(), 'tag_opinion');
                if ($opinion_tags && !is_wp_error($opinion_tags)): ?>
                <div class="opinion-tags">
                    <i class="fa fa-tags"></i>
                    <?php 
                    foreach ($opinion_tags as $tag) {
                        echo '<a href="' . get_term_link($tag) . '">' . esc_html($tag->name) . '</a>';
                    }
                    ?>
                </div>
                <?php 
                // Si no hay tags de opinion, buscar tags normales
                elseif (has_tag()): ?>
                <div class="opinion-tags">
                    <i class="fa fa-tags"></i>
                    <?php 
                    $tags = get_the_tags();
                    foreach ($tags as $tag) {
                        echo '<a href="' . get_tag_link($tag->term_id) . '">' . esc_html($tag->name) . '</a>';
                    }
                    ?>
                </div>
                <?php endif; ?>
            </footer>

            <!-- Navegación entre posts -->
            <nav class="opinion-navigation">
                <?php
                $prev_post = get_previous_post();
                $next_post = get_next_post();
                ?>
                
                <?php if ($prev_post): ?>
                <div class="nav-previous">
                    <a href="<?php echo get_permalink($prev_post->ID); ?>" rel="prev">
                        <span class="nav-subtitle"><?php _e('Opinión anterior', 'textdomain'); ?></span>
                        <span class="nav-title"><?php echo get_the_title($prev_post->ID); ?></span>
                    </a>
                </div>
                <?php endif; ?>
                
                <?php if ($next_post): ?>
                <div class="nav-next">
                    <a href="<?php echo get_permalink($next_post->ID); ?>" rel="next">
                        <span class="nav-subtitle"><?php _e('Opinión siguiente', 'textdomain'); ?></span>
                        <span class="nav-title"><?php echo get_the_title($next_post->ID); ?></span>
                    </a>
                </div>
                <?php endif; ?>
            </nav>

        </article>

        <?php 
        // Comentarios (si están habilitados)
        if (comments_open() || get_comments_number()) :
            comments_template();
        endif;
        ?>

        <?php endwhile; ?>
    </div>
    
    <div class="opinion-sidebar-area-debug">
        <?php get_sidebar(); ?>
    </div>
    
</div>

<style>
/* Layout principal - EXACTAMENTE como el debug que funciona */
.opinion-main-wrapper-debug {
    margin: 0 auto !important;
    padding: 20px !important;
    display: grid !important;
    grid-template-columns: 1fr 300px !important;
    gap: 40px !important;
    align-items: start !important;
    box-sizing: border-box !important;
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

/* NEUTRALIZAR completamente .mh-sidebar del tema */
.opinion-main-wrapper-debug .mh-sidebar {
    width: 100% !important;
    float: none !important;
    display: block !important;
    margin: 0 !important;
    padding: 0 !important;
    clear: none !important;
    position: static !important;
}

/* Estilos para widgets en la sidebar */
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

.opinion-sidebar-area-debug .textwidget {
    text-align: center !important;
}

.opinion-sidebar-area-debug .textwidget img {
    max-width: 100% !important;
    height: auto !important;
    border-radius: 6px !important;
    transition: transform 0.3s ease !important;
}

.opinion-sidebar-area-debug .textwidget img:hover {
    transform: scale(1.02) !important;
}

/* Estilos del contenido */
.opinion-header {
    margin-bottom: 30px;
    text-align: center;
}

.opinion-title {
    font-size: 2.2rem;
    line-height: 1.2;
    color: #333;
    margin: 0;
    font-weight: 700;
}

/* Recuadro gris principal */
.opinion-author-box {
    background: #f5f5f5;
    border-radius: 12px;
    padding: 25px;
    margin: 30px 0;
    border-left: 5px solid #0073aa;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.author-box-content {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

/* Foto destacada dentro del recuadro */
.opinion-featured-image {
    text-align: center;
}

.opinion-featured-image .featured-img {
    max-width: 100%;
    height: auto;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

/* Información del autor */
.opinion-author-info {
    display: flex;
    align-items: center;
    gap: 20px;
    padding: 15px 0;
    border-bottom: 1px solid #ddd;
}

.author-photo .author-photo-img {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid #fff;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.author-details {
    flex: 1;
}

.author-name {
    margin: 0 0 5px 0;
    font-size: 1.4rem;
    color: #333;
    font-weight: 600;
}

.author-title {
    margin: 0;
    color: #666;
    font-size: 1rem;
    font-style: italic;
}

/* Frase destacada */
.opinion-quote-highlight {
    text-align: center;
    padding: 20px 0;
}

.opinion-quote {
    font-size: 1.2rem;
    line-height: 1.5;
    color: #0073aa;
    font-style: italic;
    font-weight: 500;
    margin: 0;
    position: relative;
    padding: 0 30px;
    white-space: pre-line; /* Esto preserva los saltos de línea */
}

.opinion-quote::before {
    content: """;
    font-size: 2.5rem;
    color: #0073aa;
    opacity: 0.3;
    position: absolute;
    left: 0;
    top: -10px;
}

.opinion-quote::after {
    content: """;
    font-size: 2.5rem;
    color: #0073aa;
    opacity: 0.3;
    position: absolute;
    right: 0;
    bottom: -15px;
}

/* Contenido principal */
.opinion-content {
    font-size: 1.1rem;
    line-height: 1.8;
    color: #333;
    margin: 40px 0;
}

.opinion-content p {
    margin-bottom: 1.5rem;
    text-align: justify;
}

.opinion-content h2,
.opinion-content h3 {
    color: #0073aa;
    margin-top: 2rem;
    margin-bottom: 1rem;
}

/* Meta información */
.opinion-meta {
    border-top: 1px solid #eee;
    padding-top: 20px;
    margin-top: 40px;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    flex-wrap: wrap;
    gap: 20px;
}

.opinion-meta-info {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    font-size: 0.9rem;
    color: #666;
}

.opinion-meta-info span {
    display: flex;
    align-items: center;
    gap: 5px;
}

.opinion-meta-info i {
    color: #0073aa;
}

/* Tags en la esquina derecha, más pequeños */
.opinion-tags {
    font-size: 0.8rem !important;
    margin-left: auto;
}

.opinion-tags a {
    background: #f0f0f0;
    color: #666;
    text-decoration: none;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 0.75rem;
    transition: all 0.3s ease;
    display: inline-block;
    margin: 0 2px;
}

.opinion-tags a:hover {
    background: #0073aa;
    color: white;
    transform: translateY(-1px);
}

/* Navegación entre posts */
.opinion-navigation {
    margin-top: 40px;
    padding-top: 30px;
    border-top: 2px solid #f0f0f0;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.nav-previous,
.nav-next {
    padding: 20px;
    background: #f9f9f9;
    border-radius: 8px;
    transition: background-color 0.3s ease;
}

.nav-previous:hover,
.nav-next:hover {
    background: #f0f0f0;
}

.nav-previous a,
.nav-next a {
    text-decoration: none;
    color: inherit;
    display: block;
}

.nav-next {
    text-align: right;
}

.nav-subtitle {
    display: block;
    font-size: 0.8rem;
    text-transform: uppercase;
    color: #0073aa;
    margin-bottom: 5px;
    font-weight: 600;
}

.nav-title {
    display: block;
    font-weight: 600;
    color: #333;
    line-height: 1.3;
}

/* Responsive Design */
@media (max-width: 968px) {
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
    
    .opinion-title {
        font-size: 2rem;
    }
}

@media (max-width: 768px) {
    .opinion-author-box {
        padding: 20px;
        margin: 20px 0;
    }
    
    .opinion-author-info {
        flex-direction: column;
        text-align: center;
        gap: 15px;
    }
    
    .author-photo .author-photo-img {
        width: 60px;
        height: 60px;
    }
    
    .opinion-quote {
        font-size: 1.1rem;
        padding: 0 20px;
    }
    
    .opinion-navigation {
        grid-template-columns: 1fr;
        gap: 15px;
    }
    
    .nav-next {
        text-align: left;
    }
    
    .opinion-meta {
        flex-direction: column;
        gap: 15px;
    }
    
    .opinion-meta-info {
        flex-direction: column;
        gap: 10px;
    }
    
    .opinion-tags {
        margin-left: 0;
        align-self: flex-start;
    }
}

@media (max-width: 480px) {
    .opinion-title {
        font-size: 1.8rem;
    }
    
    .opinion-author-box {
        padding: 15px;
    }
    
    .opinion-content {
        font-size: 1rem;
    }
    
    .opinion-quote::before,
    .opinion-quote::after {
        display: none;
    }
}
</style>

<?php get_footer(); ?>