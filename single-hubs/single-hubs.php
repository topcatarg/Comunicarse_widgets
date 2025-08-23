<?php
/**
 * Template para post individual de hubs
 * Archivo: single-hubs.php
 * Ubicación: wp-content/themes/mh-magazine/single-hubs.php
 * Autor: Gonzalo Bianchi
 */

get_header(); 

// Obtener datos del post actual
$post_id = get_the_ID();
$categories = get_hub_post_categories($post_id);
$main_category = $categories && !is_wp_error($categories) ? $categories[0] : null;

// Función para obtener configuración de la categoría principal
function get_hub_category_config($category_slug) {
    $configs = array(
        'hub-category-ddhh-y-empresa' => array(
            'title' => 'DD.HH. y Empresa',
            'popup_titulo' => 'Sobre DD.HH. y Empresa',
            'ad_image' => get_template_directory_uri() . '/assets/ads/logo-sancorseguros.jpg',
            'ad_alt' => 'Publicidad DD.HH. y Empresa',
            'color_primary' => '#e74c3c',
            'color_secondary' => '#c0392b',
            'icon' => '⚖️',
            'gradient' => 'linear-gradient(135deg, #e74c3c 0%, #c0392b 50%, #a93226 100%)',
            'theme' => 'red'
        ),
        'hub-category-cop-cambio-climatico' => array(
            'title' => 'COP y Cambio Climático',
            'popup_titulo' => 'Sobre COP y Cambio Climático',
            'ad_image' => get_template_directory_uri() . '/assets/ads/logo-kpmg.jpg',
            'ad_alt' => 'Publicidad COP y Cambio Climático',
            'color_primary' => '#2ecc71',
            'color_secondary' => '#27ae60',
            'icon' => '🌍',
            'gradient' => 'linear-gradient(135deg, #2ecc71 0%, #27ae60 50%, #16a085 100%)',
            'theme' => 'green'
        ),
        'hub-category-economia-regenerativa' => array(
            'title' => 'Economía Regenerativa',
            'popup_titulo' => 'Sobre Economía Regenerativa',
            'ad_image' => get_template_directory_uri() . '/assets/ads/logo-syngenta.jpg',
            'ad_alt' => 'Publicidad Economía Regenerativa',
            'color_primary' => '#9b59b6',
            'color_secondary' => '#8e44ad',
            'icon' => '♻️',
            'gradient' => 'linear-gradient(135deg, #9b59b6 0%, #8e44ad 50%, #7d3c98 100%)',
            'theme' => 'purple'
        ),
        'hub-category-negocios-inclusivos-y-sociales' => array(
            'title' => 'Negocios Inclusivos y Sociales',
            'popup_titulo' => 'Sobre Negocios Inclusivos y Sociales',
            'color_primary' => '#f39c12',
            'color_secondary' => '#e67e22',
            'icon' => '🤝',
            'gradient' => 'linear-gradient(135deg, #f39c12 0%, #e67e22 50%, #d35400 100%)',
            'theme' => 'orange'
        ),
        'hub-category-packaging-sustentable' => array(
            'title' => 'Packaging Sustentable',
            'popup_titulo' => 'Sobre Packaging Sustentable',
            'color_primary' => '#1abc9c',
            'color_secondary' => '#16a085',
            'icon' => '📦',
            'gradient' => 'linear-gradient(135deg, #1abc9c 0%, #16a085 50%, #138d75 100%)',
            'theme' => 'teal'
        ),
        'hub-category-movilidad-sostenible' => array(
            'title' => 'Movilidad Sostenible',
            'popup_titulo' => 'Sobre Movilidad Sostenible',
            'color_primary' => '#3498db',
            'color_secondary' => '#2980b9',
            'icon' => '🚗',
            'gradient' => 'linear-gradient(135deg, #3498db 0%, #2980b9 50%, #1f618d 100%)',
            'theme' => 'blue'
        )
    );
    
    $default_config = array(
        'title' => 'Hubs',
        'popup_titulo' => 'Sobre este contenido',
        'ad_image' => get_template_directory_uri() . '/assets/ads/default-banner.jpg',
        'ad_alt' => 'Publicidad Hub',
        'color_primary' => '#667eea',
        'color_secondary' => '#764ba2',
        'icon' => '🏢',
        'gradient' => 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
        'theme' => 'default'
    );
    
    if (!isset($configs[$category_slug])) {
        return $default_config;
    }
    
    $config = $configs[$category_slug];
    
    // Agregar contenido del popup si no existe
    if (!isset($config['popup_contenido'])) {
        $config['popup_contenido'] = '"Con el apoyo de" es utilizado para describir contenido editorial independiente que ComunicaRSE produce en asociación con empresas y organizaciones que tienen un interés estratégico en potenciar la divulgación de algún tema de la agenda de la sustentabilidad coincidente con nuestra línea editorial.<br><br>La empresa u organización cuyo logo aparece en el contenido puede tener algún rol en sugerir temas e ideas a los cuales darles cobertura, pero tanto la producción periodística como la decisión de aprobación editorial final corren por exclusiva cuenta de los periodistas y editores de ComunicaRSE.';
    }
    
    return $config;
}

$config = $main_category ? get_hub_category_config($main_category->slug) : get_hub_category_config('default');

// Verificar si existe la imagen publicitaria
$show_left_sidebar = false;
if ($main_category && isset($config['ad_image'])) {
    $image_path = str_replace(get_template_directory_uri(), get_template_directory(), $config['ad_image']);
    $show_left_sidebar = file_exists($image_path);
}
?>

<div class="hub-single-page-wrapper theme-<?php echo $config['theme']; ?>">
    <div class="container">
        
        <!-- Título principal de la sección -->
        <div class="hub-single-title-section">
            <h1 class="hub-single-main-title" style="color: <?php echo $config['color_primary']; ?>;">
                <?php echo isset($config['title']) ? esc_html($config['title']) : ($main_category ? esc_html($main_category->name) : 'Hubs'); ?>
            </h1>
        </div>

        <!-- Layout de 3 columnas -->
        <div class="hub-single-content-wrapper <?php echo $show_left_sidebar ? 'with-left-sidebar' : 'no-left-sidebar'; ?>">
            
            <!-- Columna izquierda: Imagen publicitaria (solo si existe) -->
            <?php if ($show_left_sidebar) : ?>
                <div class="hub-left-column">
                    <div class="hub-sponsor-section">
                        <h3 class="sponsor-title">Con el apoyo de</h3>
                        <div class="sponsor-image-container">
                            <img src="<?php echo esc_url($config['ad_image']); ?>" 
                                 alt="<?php echo esc_attr($config['ad_alt']); ?>" 
                                 class="sponsor-image-single" />
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

            <!-- Columna central: Contenido del artículo -->
            <div class="hub-center-column">
                
                <?php while (have_posts()) : the_post(); ?>
                    
                    <article class="hub-single-article">
                        
                        <!-- Sin breadcrumb -->

                        <!-- Título del artículo -->
                        <header class="article-header">
                            <h1 class="article-title"><?php the_title(); ?></h1>
                        </header>

                        <!-- Imagen destacada -->
                        <?php if (has_post_thumbnail()) : ?>
                            <div class="article-featured-image">
                                <?php the_post_thumbnail('full', array('class' => 'featured-img')); ?>
                            </div>
                        <?php endif; ?>

                        <!-- Contenido del artículo -->
                        <div class="article-content">
                            <?php the_content(); ?>
                        </div>

                        <!-- Metadatos: fecha, categoría y tags (igual formato que single-opinion) -->
                        <footer class="hub-meta">
                            <div class="hub-meta-info">
                                
                                <!-- Fecha -->
                                <span class="hub-date">
                                    <i class="fa fa-calendar"></i>
                                    <?php echo get_the_date(); ?>
                                </span>

                                <!-- Categorías -->
                                <?php if ($categories && !is_wp_error($categories) && !empty($categories)) : ?>
                                    <span class="hub-categories">
                                        <i class="fa fa-folder"></i>
                                        <?php 
                                        $category_links = array();
                                        foreach($categories as $category) {
                                            $category_links[] = '<a href="' . get_hub_category_archive_url($category->slug) . '">' . esc_html($category->name) . '</a>';
                                        }
                                        echo implode(', ', $category_links);
                                        ?>
                                    </span>
                                <?php endif; ?>

                            </div>

                            <!-- Tags -->
                            <?php 
                            $tags = get_hub_post_tags($post_id);
                            if ($tags && !is_wp_error($tags) && !empty($tags)) : 
                            ?>
                                <div class="hub-tags">
                                    <i class="fa fa-tags"></i>
                                    <?php 
                                    foreach($tags as $tag) {
                                        echo '<a href="' . get_hub_tag_archive_url($tag->slug) . '">' . esc_html($tag->name) . '</a>';
                                    }
                                    ?>
                                </div>
                            <?php endif; ?>
                            
                        </footer>
                        
                    </article>
                    
                <?php endwhile; ?>
                
            </div>

            <!-- Columna derecha: Sidebar del tema -->
            <div class="hub-right-column">
                <?php get_sidebar(); ?>
            </div>
            
        </div>
    </div>
</div>

<style>
/* CSS para single-hubs.php - Formato de 3 columnas */

.hub-single-page-wrapper {
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

/* Título principal */
.hub-single-title-section {
    background: #f8f9fa;
    padding: 40px 0 20px 0;
    border-bottom: 1px solid #eee;
}

.hub-single-main-title {
    font-size: 2.5rem;
    font-weight: 700;
    margin: 0;
    text-align: left;
}

/* Layout de contenido - Base */
.hub-single-content-wrapper {
    display: grid;
    gap: 30px;
    padding: 30px 0;
    min-height: 600px;
}

/* Layout CON sidebar izquierda */
.hub-single-content-wrapper.with-left-sidebar {
    grid-template-columns: 160px 1fr 300px;
}

/* Layout SIN sidebar izquierda */
.hub-single-content-wrapper.no-left-sidebar {
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

.sponsor-image-container {
    padding: 15px;
}

.sponsor-image-single {
    width: 100%;
    height: auto;
    border-radius: 6px;
    transition: transform 0.3s ease;
}

.sponsor-image-single:hover {
    transform: scale(1.02);
}

/* Columna central */
.hub-center-column {
    background: #fff;
    box-sizing: border-box;
    overflow: hidden;
}

/* Artículo individual */
.hub-single-article {
    max-width: 100%;
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

/* Header del artículo */
.article-header {
    margin-bottom: 30px;
}

.article-title {
    font-size: 2.5rem;
    font-weight: 700;
    line-height: 1.2;
    color: #333;
    margin: 0;
}

/* Imagen destacada */
.article-featured-image {
    margin-bottom: 30px;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
}

.featured-img {
    width: 100%;
    height: auto;
    display: block;
}

/* Contenido del artículo */
.article-content {
    font-size: 1.1rem;
    line-height: 1.8;
    color: #333;
    margin-bottom: 40px;
}

.article-content p {
    margin-bottom: 1.5em;
}

.article-content h2,
.article-content h3,
.article-content h4 {
    margin-top: 2em;
    margin-bottom: 1em;
    font-weight: 600;
}

.article-content h2 {
    font-size: 1.8rem;
    color: #2ecc71;
}

.article-content h3 {
    font-size: 1.5rem;
    color: #333;
}

/* Metadatos del artículo (igual formato que single-opinion) */
.hub-meta {
    border-top: 1px solid #eee;
    padding-top: 20px;
    margin-top: 40px;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    flex-wrap: wrap;
    gap: 20px;
}

.hub-meta-info {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    font-size: 0.9rem;
    color: #666;
}

.hub-meta-info span {
    display: flex;
    align-items: center;
    gap: 5px;
}

.hub-meta-info i {
    color: #0073aa;
}

.hub-categories a {
    color: #666;
    text-decoration: none;
    transition: color 0.3s ease;
}

.hub-categories a:hover {
    color: #2ecc71;
}

/* Tags en la esquina derecha, más pequeños (igual que single-opinion) */
.hub-tags {
    font-size: 0.8rem !important;
    margin-left: auto;
}

.hub-tags a {
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

.hub-tags a:hover {
    background: #2ecc71;
    color: white;
    transform: translateY(-1px);
}

/* Columna derecha */
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

/* Responsive */
@media (max-width: 768px) {
    .hub-single-content-wrapper.with-left-sidebar,
    .hub-single-content-wrapper.no-left-sidebar {
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
    
    .article-title {
        font-size: 2rem;
    }
    
    .hub-single-main-title {
        font-size: 2rem;
    }
}

@media (max-width: 480px) {
    .container {
        padding: 0 15px;
    }
    
    .hub-single-content-wrapper {
        padding: 20px 0;
    }
    
    .article-title {
        font-size: 1.8rem;
    }
    
    .meta-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 5px;
    }
    
    .hub-meta {
        flex-direction: column;
        gap: 15px;
    }
    
    .hub-tags {
        margin-left: 0;
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