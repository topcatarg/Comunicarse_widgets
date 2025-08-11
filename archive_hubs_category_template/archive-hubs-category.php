<?php
/**
 * Template para archivo de categorías de hubs - Versión con Grid
 * Replica el diseño del sitio original de Drupal con grid de 3 columnas
 * Archivo: archive-hubs-category.php
 * Ubicación: wp-content/themes/mh-magazine/archive-hubs-category.php
 */

get_header(); 

// Obtener la categoría actual
$current_category = get_queried_object();
$category_slug = $current_category->slug;

// Función para obtener configuración universal (todos usan el mismo color verde)
function get_category_config($category_slug) {
    // Color verde universal para todos los hubs
    $color_primary = '#2ecc71';
    $color_secondary = '#27ae60';
    
    $configs = array(
        'hub-category-ddhh-y-empresa' => array(
            'title' => 'DD.HH. y Empresa',
            'popup_titulo' => 'Sobre DD.HH. y Empresa',
            'ad_image' => get_template_directory_uri() . '/assets/ads/logo-sancorseguros.jpg',
            'ad_alt' => 'Publicidad DD.HH. y Empresa'
        ),
        'hub-category-cop-cambio-climatico' => array(
            'title' => 'COP y Cambio Climático',
            'popup_titulo' => 'Sobre COP y Cambio Climático',
            'ad_image' => get_template_directory_uri() . '/assets/ads/logo-kpmg.jpg',
            'ad_alt' => 'Publicidad COP y Cambio Climático'
        ),
        'hub-category-economia-regenerativa' => array(
            'title' => 'Economía Regenerativa',
            'popup_titulo' => 'Sobre Economía Regenerativa',
            'ad_image' => get_template_directory_uri() . '/assets/ads/logo-syngenta.jpg',
            'ad_alt' => 'Publicidad Economía Regenerativa'
        ),
        'hub-category-negocios-inclusivos-y-sociales' => array(
            'title' => 'Negocios Inclusivos y Sociales',
            'popup_titulo' => 'Sobre Negocios Inclusivos y Sociales',
        ),
                'hub-category-packaging-sustentable' => array(
            'title' => 'Packaging Sustentable',
            'popup_titulo' => 'Sobre Packaging Sustentable',
        ),
        

    );
    
    // Obtener configuración específica o usar valores por defecto
    $specific_config = isset($configs[$category_slug]) ? $configs[$category_slug] : array();
    
    // Configuración final universal con fallbacks
    return array(
        'title' => isset($specific_config['title']) ? $specific_config['title'] : $current_category->name,
        'color_primary' => $color_primary,
        'color_secondary' => $color_secondary,
        'icon' => '🏢',
        'gradient' => 'linear-gradient(135deg, ' . $color_primary . ' 0%, ' . $color_secondary . ' 100%)',
        'ad_image' => isset($specific_config['ad_image']) ? $specific_config['ad_image'] : get_template_directory_uri() . '/assets/ads/default-banner.jpg',
        'ad_alt' => isset($specific_config['ad_alt']) ? $specific_config['ad_alt'] : 'Publicidad Hub',
        'popup_titulo' => isset($specific_config['popup_titulo']) ? $specific_config['popup_titulo'] : 'Sobre este contenido',
        'popup_contenido' => isset($specific_config['popup_contenido']) ? $specific_config['popup_contenido'] : '"Con el apoyo de" es utilizado para describir contenido editorial independiente que ComunicaRSE produce en asociación con empresas y organizaciones que tienen un interés estratégico en potenciar la divulgación de algún tema de la agenda de la sustentabilidad coincidente con nuestra línea editorial.<br><br>La empresa u organización cuyo logo aparece en el contenido puede tener algún rol en sugerir temas e ideas a los cuales darles cobertura, pero tanto la producción periodística como la decisión de aprobación editorial final corren por exclusiva cuenta de los periodistas y editores de ComunicaRSE.'
    );
}

// Obtener configuración para esta categoría
$config = get_category_config($category_slug);

// Verificar si hay imagen publicitaria para mostrar la columna izquierda
$image_path = str_replace(get_template_directory_uri(), get_template_directory(), $config['ad_image']);
$show_left_sidebar = file_exists($image_path);
?>

<div class="hub-category-page-wrapper">
    
    <!-- Título principal verde como en el sitio original -->
    <div class="hub-category-title-section">
        <div class="container">
            <h1 class="hub-category-main-title" style="color: <?php echo $config['color_primary']; ?>;">
                <?php echo esc_html($config['title']); ?>
            </h1>
        </div>
    </div>

    <!-- Layout principal -->
    <div class="container">
        <div class="hub-category-content-wrapper <?php echo $show_left_sidebar ? 'with-left-sidebar' : 'no-left-sidebar'; ?>">
            
            <?php if ($show_left_sidebar) : ?>
                <!-- Columna izquierda: "Con el apoyo de" y publicidad -->
                <div class="hub-left-column">
                    
                    <!-- Sección "Con el apoyo de" -->
                    <div class="hub-sponsor-section">
                        <h3 class="sponsor-title">Con el apoyo de</h3>
                        
                        <!-- Imagen publicitaria -->
                        <div class="sponsor-image-container">
                            <?php 
                            // Verificar si existe la imagen específica
                            if (file_exists($image_path)) : 
                            ?>
                                <!-- Imagen real -->
                                <img src="<?php echo esc_url($config['ad_image']); ?>" 
                                     alt="<?php echo esc_attr($config['ad_alt']); ?>" 
                                     class="sponsor-ad-image" />
                            <?php else : ?>
                                <!-- Placeholder cuando no hay imagen -->
                                <div class="sponsor-ad-placeholder" style="background: linear-gradient(45deg, <?php echo $config['color_primary']; ?>20, <?php echo $config['color_secondary']; ?>20);">
                                    <div class="ad-content">
                                        <div class="ad-icon"><?php echo $config['icon']; ?></div>
                                        <h4>Publicidad <?php echo esc_html($config['title']); ?></h4>
                                        <p>Espacio publicitario vertical<br>300x250 px</p>
                                        <small>Imagen: <?php echo basename($config['ad_image']); ?></small>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Link "Sobre este contenido" con popup -->
                        <div class="hub-sobre-contenido-link">
                            <a href="#" class="sobre-contenido-trigger">Sobre este contenido</a>
                            
                            <!-- Popup overlay -->
                            <div class="popup-overlay" style="display: none;">
                                <div class="popup-contenido">
                                    <span class="popup-cerrar">&times;</span>
                                    <h3><?php echo esc_html($config['popup_titulo']); ?></h3>
                                    <div class="popup-texto">
                                        <?php echo wp_kses_post($config['popup_contenido']); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </div>
            <?php endif; ?>

                <!-- Columna central: Lista de publicaciones -->
                <div class="hub-center-column">

                    <?php if (have_posts()) : ?>
                        
                        <!-- Primera publicación destacada -->
                        <?php 
                        $first_post_shown = false;
                        while (have_posts()) : the_post(); 
                            if (!$first_post_shown) :
                                $first_post_shown = true;
                        ?>
                            <article class="hub-featured-first-post">
                                <?php if (has_post_thumbnail()) : ?>
                                    <div class="hub-first-image">
                                        <a href="<?php the_permalink(); ?>">
                                            <?php the_post_thumbnail('large', array('class' => 'hub-first-thumb')); ?>
                                        </a>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="hub-first-content">
                                    <h2 class="hub-first-title">
                                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                    </h2>
                                    
                                    <?php if (has_excerpt() || get_the_content()) : ?>
                                        <div class="hub-first-excerpt">
                                            <?php 
                                            if (has_excerpt()) {
                                                the_excerpt();
                                            } else {
                                                echo wp_trim_words(get_the_content(), 50, '...');
                                            }
                                            ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </article>
                        <?php 
                            break; // Solo mostrar el primer post aquí
                            endif;
                        endwhile; 
                        
                        // Resetear para mostrar el resto
                        rewind_posts();
                        $post_counter = 0;
                        ?>

                        <!-- Grid de publicaciones restantes -->
                        <div class="hub-grid-container">
                            <?php while (have_posts()) : the_post(); 
                                $post_counter++;
                                // Saltarse el primer post ya mostrado
                                if ($post_counter === 1) continue;
                            ?>
                                <article class="hub-grid-item">
                                    <?php if (has_post_thumbnail()) : ?>
                                        <div class="hub-grid-image">
                                            <a href="<?php the_permalink(); ?>">
                                                <?php the_post_thumbnail('medium', array('class' => 'hub-grid-thumb')); ?>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="hub-grid-content">
                                        <h3 class="hub-grid-title">
                                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                        </h3>
                                    </div>
                                    
                                    <!-- Enlace overlay completo -->
                                    <a href="<?php the_permalink(); ?>" class="hub-grid-link-overlay" aria-label="Leer artículo completo"></a>
                                </article>
                            <?php endwhile; ?>
                        </div>

                        <!-- Paginación -->
                        <?php if ($wp_query->max_num_pages > 1) : ?>
                            <div class="hubs-pagination">
                                <?php
                                the_posts_pagination(array(
                                    'mid_size' => 2,
                                    'prev_text' => '← Anterior',
                                    'next_text' => 'Siguiente →',
                                    'before_page_number' => '<span class="screen-reader-text">Página </span>',
                                ));
                                ?>
                            </div>
                        <?php endif; ?>
                        
                    <?php else : ?>
                        <div class="no-hubs-found">
                            <h3>No hay publicaciones disponibles</h3>
                            <p>Aún no hay contenido publicado en esta categoría de hub.</p>
                        </div>
                    <?php endif; ?>
                    
                </div>

                <!-- Columna derecha: Solo Sidebar del tema -->
                <div class="hub-right-column">
                    <?php get_sidebar(); ?>
                </div>
        </div>
    </div>
</div>

<style>
/* CSS para el template con grid de categorías de hubs */

.hub-category-page-wrapper {
    max-width: 100%;
    margin: 0;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
}

/* Container principal */
.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

/* Título principal verde */
.hub-category-title-section {
    background: #f8f9fa;
    padding: 40px 0 20px 0;
    border-bottom: 1px solid #eee;
}

.hub-category-main-title {
    font-size: 2.5rem;
    font-weight: 700;
    margin: 0;
    text-align: left;
}

/* Layout de contenido - Base */
.hub-category-content-wrapper {
    display: grid;
    gap: 30px;
    padding: 30px 0;
    min-height: 600px;
}

/* Layout CON sidebar izquierda */
.hub-category-content-wrapper.with-left-sidebar {
    grid-template-columns: 160px 1fr 300px;
}

/* Layout SIN sidebar izquierda */
.hub-category-content-wrapper.no-left-sidebar {
    grid-template-columns: 1fr 300px;
}

/* Columna izquierda */
.hub-left-column {
    background: #fff;
    padding: 0;
}

/* Sección "Con el apoyo de" */
.hub-sponsor-section {
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.sponsor-title {
    background: #f8f9fa;
    margin: 0;
    padding: 15px 20px;
    font-size: 1.1rem;
    font-weight: 600;
    color: #333;
    border-bottom: 1px solid #eee;
}

/* Imagen publicitaria */
.sponsor-image-container {
    padding: 15px;
}

.sponsor-ad-image {
    width: 100%;
    height: auto;
    max-height: 250px;
    object-fit: cover;
    border-radius: 8px;
    display: block;
}

.sponsor-ad-placeholder {
    width: 100%;
    height: 200px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    border: 2px dashed #ddd;
    text-align: center;
}

.sponsor-ad-placeholder .ad-content {
    padding: 15px;
}

.sponsor-ad-placeholder .ad-icon {
    font-size: 2rem;
    margin-bottom: 8px;
}

.sponsor-ad-placeholder h4 {
    margin: 8px 0;
    font-size: 0.9rem;
    color: #333;
}

.sponsor-ad-placeholder p {
    margin: 0 0 8px 0;
    color: #666;
    font-size: 0.8rem;
}

.sponsor-ad-placeholder small {
    color: #999;
    font-size: 0.7rem;
    font-style: italic;
}

/* Link "Sobre este contenido" */
.hub-sobre-contenido-link {
    padding: 10px 15px;
    border-top: 1px solid #eee;
    position: relative;
}

.sobre-contenido-trigger {
    color: #666;
    text-decoration: none;
    font-size: 0.8rem;
    display: inline-flex;
    align-items: center;
    transition: color 0.3s ease;
}

.sobre-contenido-trigger:hover {
    color: #2ecc71;
    text-decoration: underline;
}

.sobre-contenido-trigger::before {
    content: "ℹ️";
    margin-right: 6px;
    font-size: 0.9rem;
}

/* Popup styles */
.popup-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.6);
    z-index: 10000;
    display: flex;
    align-items: center;
    justify-content: center;
}

.popup-contenido {
    background: white;
    padding: 30px;
    border-radius: 12px;
    max-width: 500px;
    width: 90%;
    max-height: 80vh;
    overflow-y: auto;
    position: relative;
    box-shadow: 0 15px 50px rgba(0, 0, 0, 0.3);
}

.popup-cerrar {
    position: absolute;
    top: 15px;
    right: 20px;
    font-size: 24px;
    font-weight: bold;
    cursor: pointer;
    color: #999;
    line-height: 1;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: all 0.3s ease;
}

.popup-cerrar:hover {
    color: #333;
    background: #f5f5f5;
}

.popup-contenido h3 {
    margin: 0 0 20px 0;
    color: #333;
    font-size: 1.4rem;
    font-weight: 600;
    padding-right: 40px;
}

.popup-texto {
    color: #555;
    line-height: 1.6;
    font-size: 1rem;
}

/* Columna central */
.hub-center-column {
    background: #fff;
    width: 100%;
    max-width: 100%;
    box-sizing: border-box;
    overflow: hidden;
}

/* Columna derecha */
.hub-right-column {
    background: #fff;
}

/* Primera publicación destacada */
.hub-featured-first-post {
    display: flex;
    gap: 25px;
    padding: 30px;
    background: white;
    border-radius: 15px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    border: 2px solid #2ecc71;
    margin-bottom: 40px;
    width: 100%;
    box-sizing: border-box;
}

.hub-first-image {
    flex: 0 0 300px;
    max-width: 300px;
}

.hub-first-thumb {
    width: 100%;
    height: 200px;
    object-fit: cover;
    border-radius: 12px;
    display: block;
    transition: transform 0.3s ease;
}

.hub-featured-first-post:hover .hub-first-thumb {
    transform: scale(1.02);
}

.hub-first-content {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 15px;
    min-width: 0;
}

.hub-first-title {
    margin: 0;
    font-size: 1.6rem;
    font-weight: 700;
    line-height: 1.3;
}

.hub-first-title a {
    color: #333;
    text-decoration: none;
    transition: color 0.3s ease;
}

.hub-first-title a:hover {
    color: #2ecc71;
}

.hub-first-excerpt {
    color: #555;
    line-height: 1.6;
    font-size: 1.05rem;
}

/* Grid de publicaciones - 3 columnas como en archive-opinion */
.hub-grid-container {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 25px;
    margin-bottom: 40px;
}

/* Cards del grid */
.hub-grid-item {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    overflow: hidden;
    transition: all 0.3s ease;
    border: 1px solid #e9ecef;
    position: relative;
    cursor: pointer;
}

.hub-grid-item:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.hub-grid-image {
    width: 100%;
    height: 180px;
    overflow: hidden;
}

.hub-grid-thumb {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.hub-grid-item:hover .hub-grid-thumb {
    transform: scale(1.05);
}

.hub-grid-content {
    padding: 15px;
}

.hub-grid-title {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 600;
    line-height: 1.3;
}

.hub-grid-title a {
    color: #333;
    text-decoration: none;
    transition: color 0.3s ease;
}

.hub-grid-title a:hover {
    color: #2ecc71;
}

/* Enlace overlay para las cards del grid */
.hub-grid-link-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    text-decoration: none;
    z-index: 2;
}

/* Columna derecha - Sidebar del tema como en archive-opinion */
.hub-right-column {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 10px;
    border: 1px solid #e9ecef;
    position: sticky;
    top: 20px;
    max-height: calc(100vh - 40px);
    overflow-y: auto;
    box-sizing: border-box;
    width: 300px;
    max-width: 300px;
    min-width: 300px;
}

/* Neutralizar estilos del tema en la sidebar */
.hub-right-column .mh-sidebar {
    width: 100% !important;
    float: none !important;
    display: block !important;
    margin: 0 !important;
    padding: 0 !important;
    clear: none !important;
    position: static !important;
}

/* Estilos para widgets en la sidebar */
.hub-right-column .widget {
    margin-bottom: 25px !important;
    background: #fff !important;
    padding: 15px !important;
    border-radius: 8px !important;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05) !important;
}

.hub-right-column .widget:last-child {
    margin-bottom: 0 !important;
}

.hub-right-column .widget-title {
    margin: 0 0 15px 0 !important;
    font-size: 1.1rem !important;
    color: #2ecc71 !important;
    font-weight: 600 !important;
    border-bottom: 2px solid #2ecc71 !important;
    padding-bottom: 8px !important;
}

/* Mensaje cuando no hay publicaciones */
.no-hubs-found {
    text-align: center;
    padding: 60px 20px;
    color: #666;
}

.no-hubs-found h3 {
    color: #333;
    margin-bottom: 15px;
}

/* Paginación */
.hubs-pagination {
    margin-top: 50px;
    text-align: center;
}

.hubs-pagination .nav-links {
    display: flex;
    justify-content: center;
    gap: 10px;
    flex-wrap: wrap;
}

.hubs-pagination a,
.hubs-pagination .current {
    padding: 10px 15px;
    border-radius: 6px;
    text-decoration: none;
    transition: all 0.3s ease;
}

.hubs-pagination a {
    background: white;
    color: #2ecc71;
    border: 1px solid #2ecc71;
}

.hubs-pagination a:hover {
    background: #2ecc71;
    color: white;
}

.hubs-pagination .current {
    background: #2ecc71;
    color: white;
    border: 1px solid #2ecc71;
}

/* Responsive */
@media (max-width: 768px) {
    .hub-category-content-wrapper.with-left-sidebar,
    .hub-category-content-wrapper.no-left-sidebar {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .hub-left-column {
        order: 3;
    }
    
    .hub-center-column {
        order: 1;
    }
    
    .hub-right-column {
        order: 2;
        width: 100%;
        max-width: none;
        min-width: 0;
        position: static;
        max-height: none;
        overflow-y: visible;
    }
    
    .hub-category-main-title {
        font-size: 2rem;
        text-align: center;
    }
    
    /* Primera publicación en mobile */
    .hub-featured-first-post {
        flex-direction: column;
        gap: 15px;
        padding: 20px;
    }
    
    .hub-first-image {
        flex: none;
        max-width: 100%;
    }
    
    .hub-first-thumb {
        height: 200px;
    }
    
    /* Grid de publicaciones en mobile - 1 columna */
    .hub-grid-container {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .hub-grid-content {
        padding: 12px;
    }
    
    .sponsor-ad-placeholder {
        height: 150px;
    }
}

@media (max-width: 480px) {
    .container {
        padding: 0 15px;
    }
    
    .hub-category-content-wrapper {
        padding: 20px 0;
    }
    
    .hub-featured-first-post {
        padding: 15px;
    }
    
    .hub-first-title {
        font-size: 1.3rem;
    }
    
    .hub-grid-content {
        padding: 12px;
    }
    
    .hub-grid-title {
        font-size: 1rem;
    }
}
</style>

<script>
// JavaScript para manejar el popup
document.addEventListener('DOMContentLoaded', function() {
    // Abrir popup
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('sobre-contenido-trigger')) {
            e.preventDefault();
            const popup = e.target.parentNode.querySelector('.popup-overlay');
            if (popup) {
                popup.style.display = 'flex';
                document.body.style.overflow = 'hidden';
            }
        }
        
        // Cerrar popup
        if (e.target.classList.contains('popup-cerrar') || e.target.classList.contains('popup-overlay')) {
            closePopup();
        }
    });
    
    // Cerrar con Escape
    document.addEventListener('keydown', function(e) {
        if (e.keyCode === 27) {
            closePopup();
        }
    });
    
    function closePopup() {
        const popups = document.querySelectorAll('.popup-overlay');
        popups.forEach(popup => {
            popup.style.display = 'none';
        });
        document.body.style.overflow = '';
    }
    
    // Prevenir que el clic en el contenido del popup lo cierre
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('popup-contenido') || e.target.closest('.popup-contenido')) {
            e.stopPropagation();
        }
    });
});
</script>

<?php get_footer(); ?>