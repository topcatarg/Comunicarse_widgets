<?php
/**
 * Template para mostrar una investigación individual
 * 
 * @author Gonzalo Bianchi
 * @package MH Magazine Theme
 * @since 1.0
 */

get_header(); ?>

<div class="mh-wrapper clearfix">
    <div id="main-content" class="mh-content">
        <?php while (have_posts()) : the_post(); ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class('investigacion-single'); ?>>
                
                <!-- Título Principal -->
                <header class="investigacion-header">
                    <h1 class="investigacion-title"><?php the_title(); ?></h1>
                </header>

                <!-- Imagen Destacada -->
                <?php if (has_post_thumbnail()) : ?>
                <div class="investigacion-featured-image">
                    <?php the_post_thumbnail('large', array('class' => 'img-responsive')); ?>
                </div>
                <?php endif; ?>

                <!-- Contenido Principal -->
                <div class="investigacion-content entry-content">
                    <?php the_content(); ?>
                </div>

                <!-- Archivos Adjuntos -->
                <?php 
                // Usar la función del plugin si existe, sino usar get_post_meta directamente
                if (function_exists('the_investigacion_archivos_adjuntos')) {
                    the_investigacion_archivos_adjuntos();
                } else {
                    // Fallback manual
                    $archivos = get_post_meta(get_the_ID(), '_investigacion_archivos_adjuntos', true);
                    if (!empty($archivos) && is_array($archivos)) : ?>
                        <div class="investigacion-archivos">
                            <h3><i class="fa fa-download"></i> Archivos Adjuntos</h3>
                            <ul class="archivos-list">
                                <?php foreach($archivos as $archivo) : ?>
                                    <?php if (!empty($archivo['url'])) : ?>
                                        <li class="archivo-item">
                                            <div class="archivo-icon">
                                                <i class="fa fa-file-pdf-o"></i>
                                            </div>
                                            <div class="archivo-info">
                                                <div class="archivo-titulo">
                                                    <a href="<?php echo esc_url($archivo['url']); ?>" 
                                                       class="archivo-link" 
                                                       target="_blank" 
                                                       rel="noopener">
                                                        <?php echo !empty($archivo['titulo']) ? esc_html($archivo['titulo']) : 'Descargar archivo'; ?>
                                                    </a>
                                                </div>
                                                <?php if (!empty($archivo['descripcion'])) : ?>
                                                    <p class="archivo-descripcion"><?php echo esc_html($archivo['descripcion']); ?></p>
                                                <?php endif; ?>
                                                <?php if (!empty($archivo['size'])) : ?>
                                                    <span class="archivo-size">(<?php echo size_format($archivo['size']); ?>)</span>
                                                <?php endif; ?>
                                            </div>
                                        </li>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif;
                } ?>

                <!-- Meta información DESPUÉS del contenido (como en single-opinion.php) -->
                <footer class="investigacion-meta">
                    <div class="investigacion-meta-info">
                        <span class="investigacion-date">
                            <i class="fa fa-calendar"></i>
                            <?php echo get_the_date(); ?>
                        </span>
                        
                        <?php if (function_exists('get_investigacion_post_categories') && get_investigacion_post_categories()) : ?>
                        <span class="investigacion-categories">
                            <i class="fa fa-folder"></i>
                            <?php the_investigacion_categories(); ?>
                        </span>
                        <?php endif; ?>
                        
                        <?php if (function_exists('get_investigacion_post_tags') && get_investigacion_post_tags()) : ?>
                        <span class="investigacion-tags">
                            <i class="fa fa-tags"></i>
                            <?php the_investigacion_tags(); ?>
                        </span>
                        <?php endif; ?>
                    </div>
                </footer>

                <!-- Navegación entre investigaciones -->
                <div class="investigacion-navigation">
                    <div class="nav-links">
                        <?php
                        $prev_post = get_previous_post(true, '', 'category_investigacion');
                        $next_post = get_next_post(true, '', 'category_investigacion');
                        ?>
                        
                        <?php if ($prev_post) : ?>
                            <div class="nav-previous">
                                <a href="<?php echo get_permalink($prev_post); ?>" rel="prev">
                                    <i class="fa fa-arrow-left"></i>
                                    <span>Anterior: <?php echo get_the_title($prev_post); ?></span>
                                </a>
                            </div>
                        <?php endif; ?>

                        <?php if ($next_post) : ?>
                            <div class="nav-next">
                                <a href="<?php echo get_permalink($next_post); ?>" rel="next">
                                    <span>Siguiente: <?php echo get_the_title($next_post); ?></span>
                                    <i class="fa fa-arrow-right"></i>
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Investigaciones Relacionadas -->
                <?php
                $related_args = array(
                    'post_type' => 'investigaciones',
                    'posts_per_page' => 3,
                    'post__not_in' => array(get_the_ID()),
                    'orderby' => 'rand'
                );

                // Si tiene categorías, buscar por categoría
                if ($categories && !is_wp_error($categories)) {
                    $category_ids = wp_list_pluck($categories, 'term_id');
                    $related_args['tax_query'] = array(
                        array(
                            'taxonomy' => 'category_investigacion',
                            'field'    => 'term_id',
                            'terms'    => $category_ids,
                        ),
                    );
                }

                $related_query = new WP_Query($related_args);
                
                if ($related_query->have_posts()) : ?>
                    <div class="investigaciones-relacionadas">
                        <h3><i class="fa fa-lightbulb-o"></i> Investigaciones Relacionadas</h3>
                        <div class="relacionadas-grid">
                            <?php while ($related_query->have_posts()) : $related_query->the_post(); ?>
                                <div class="relacionada-item">
                                    <?php if (has_post_thumbnail()) : ?>
                                        <div class="relacionada-thumbnail">
                                            <a href="<?php the_permalink(); ?>">
                                                <?php the_post_thumbnail('medium', array('class' => 'img-responsive')); ?>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="relacionada-content">
                                        <h4 class="relacionada-title">
                                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                        </h4>
                                        
                                        <?php if (has_excerpt()) : ?>
                                            <p class="relacionada-excerpt"><?php the_excerpt(); ?></p>
                                        <?php else : ?>
                                            <p class="relacionada-excerpt"><?php echo wp_trim_words(get_the_content(), 15); ?></p>
                                        <?php endif; ?>
                                        
                                        <a href="<?php the_permalink(); ?>" class="relacionada-link">
                                            Leer más <i class="fa fa-arrow-right"></i>
                                        </a>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                    <?php wp_reset_postdata(); ?>
                <?php endif; ?>

            </article>
        <?php endwhile; ?>
    </div>

    <?php get_sidebar(); ?>
</div>

<style>
/* Estilos específicos para investigaciones - Compatible con MH Magazine */
.investigacion-single {
    background: #fff;
    padding: 30px;
    margin-bottom: 30px;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.investigacion-header {
    margin-bottom: 25px;
    border-bottom: 2px solid #f1f1f1;
    padding-bottom: 20px;
}

.investigacion-title {
    font-size: 2.2em;
    color: #333;
    margin-bottom: 0;
    line-height: 1.3;
}

.investigacion-featured-image {
    margin: 25px 0;
    text-align: center;
}

.investigacion-featured-image img {
    max-width: 100%;
    height: auto;
    border-radius: 8px;
}

.investigacion-taxonomies {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 6px;
    margin: 20px 0;
    border-left: 4px solid #0073aa;
}

.investigacion-categories,
.investigacion-tags {
    margin-bottom: 8px;
}

.investigacion-categories:last-child,
.investigacion-tags:last-child {
    margin-bottom: 0;
}

.investigacion-taxonomies strong {
    color: #333;
}

.investigacion-taxonomies a {
    color: #0073aa;
    text-decoration: none;
    margin-left: 5px;
}

.investigacion-taxonomies a:hover {
    text-decoration: underline;
}

.investigacion-content {
    font-size: 16px;
    line-height: 1.7;
    margin: 30px 0;
}

.investigacion-archivos {
    background: #f8f9fa;
    padding: 25px;
    border-radius: 8px;
    margin: 30px 0;
    border: 1px solid #e9ecef;
}

.investigacion-archivos h3 {
    margin-bottom: 20px;
    color: #333;
    font-size: 1.3em;
}

.archivos-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.archivo-item {
    display: flex;
    align-items: flex-start;
    padding: 15px;
    background: white;
    border: 1px solid #ddd;
    border-radius: 6px;
    margin-bottom: 10px;
    transition: box-shadow 0.3s ease;
}

.archivo-item:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.archivo-icon {
    margin-right: 15px;
    color: #dc3545;
    font-size: 1.5em;
}

.archivo-info {
    flex: 1;
}

.archivo-titulo {
    margin-bottom: 5px;
}

.archivo-link {
    color: #0073aa;
    text-decoration: none;
    font-weight: 600;
    font-size: 16px;
}

.archivo-link:hover {
    text-decoration: underline;
}

.archivo-descripcion {
    color: #555;
    font-size: 14px;
    margin: 8px 0 5px 0;
    line-height: 1.5;
}

.archivo-size {
    color: #666;
    font-size: 13px;
    font-style: italic;
}

.investigacion-navigation {
    margin: 40px 0;
    padding: 20px 0;
    border-top: 1px solid #e9ecef;
}

.nav-links {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.nav-previous a,
.nav-next a {
    color: #0073aa;
    text-decoration: none;
    padding: 10px 15px;
    border: 1px solid #0073aa;
    border-radius: 5px;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 8px;
    max-width: 300px;
}

.nav-previous a:hover,
.nav-next a:hover {
    background: #0073aa;
    color: white;
}

.investigaciones-relacionadas {
    margin-top: 40px;
    padding-top: 30px;
    border-top: 2px solid #f1f1f1;
}

.investigaciones-relacionadas h3 {
    margin-bottom: 25px;
    color: #333;
    font-size: 1.4em;
}

.relacionadas-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 25px;
}

.relacionada-item {
    background: #fff;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.relacionada-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.relacionada-thumbnail img {
    width: 100%;
    height: 180px;
    object-fit: cover;
}

.relacionada-content {
    padding: 20px;
}

.relacionada-title {
    margin-bottom: 12px;
    font-size: 1.1em;
}

.relacionada-title a {
    color: #333;
    text-decoration: none;
}

.relacionada-title a:hover {
    color: #0073aa;
}

.relacionada-excerpt {
    color: #666;
    font-size: 14px;
    line-height: 1.5;
    margin-bottom: 15px;
}

.relacionada-link {
    color: #0073aa;
    text-decoration: none;
    font-weight: 600;
    font-size: 14px;
}

.relacionada-link:hover {
    text-decoration: underline;
}

/* Responsive */
@media (max-width: 768px) {
    .investigacion-single {
        padding: 20px;
    }
    
    .investigacion-title {
        font-size: 1.8em;
    }
    
    .investigacion-meta-info {
        flex-direction: column;
        gap: 10px;
    }
    
    .nav-links {
        flex-direction: column;
        gap: 15px;
    }
    
    .nav-previous a,
    .nav-next a {
        max-width: none;
        text-align: center;
    }
    
    .relacionadas-grid {
        grid-template-columns: 1fr;
    }
    
    .archivo-item {
        flex-direction: column;
        text-align: center;
    }
    
    .archivo-icon {
        margin-right: 0;
        margin-bottom: 10px;
    }
}
</style>

<?php get_footer(); ?>