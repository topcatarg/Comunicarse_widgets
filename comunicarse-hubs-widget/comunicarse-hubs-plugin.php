<?php
/**
 * Plugin Name: ComunicarSe Hubs Widget
 * Description: Widget configurable de 6 hubs para la página principal
 * Version: 1.0.0
 * Author: Tu Nombre
 * Text Domain: comunicarse-hubs
 */

// Evitar acceso directo
if (!defined('ABSPATH')) {
    exit;
}

// Definir constantes del plugin
define('COMUNICARSE_HUBS_VERSION', '1.0.0');
define('COMUNICARSE_HUBS_PLUGIN_URL', plugin_dir_url(__FILE__));
define('COMUNICARSE_HUBS_PLUGIN_PATH', plugin_dir_path(__FILE__));

/**
 * Clase principal del plugin
 */
class ComunicarSeHubsPlugin 
{
    public function __construct() 
    {
        add_action('init', array($this, 'init'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('wp_ajax_save_hubs_config', array($this, 'save_hubs_config'));
        add_action('wp_ajax_get_hubs_config', array($this, 'get_hubs_config'));
        add_shortcode('comunicarse_hubs', array($this, 'render_hubs_shortcode'));
        add_action('widgets_init', array($this, 'register_widget'));
    }

    public function init() 
    {
        // Cargar traducciones si las hay
        load_plugin_textdomain('comunicarse-hubs', false, dirname(plugin_basename(__FILE__)) . '/languages');
        
        // Crear tabla si no existe
        $this->create_table();
    }

    public function enqueue_scripts() 
    {
        wp_enqueue_style(
            'comunicarse-hubs-style',
            COMUNICARSE_HUBS_PLUGIN_URL . 'assets/style.css',
            array(),
            COMUNICARSE_HUBS_VERSION
        );

        wp_enqueue_script(
            'comunicarse-hubs-script',
            COMUNICARSE_HUBS_PLUGIN_URL . 'assets/script.js',
            array('jquery'),
            COMUNICARSE_HUBS_VERSION,
            true
        );

        // Localizar script para AJAX
        wp_localize_script('comunicarse-hubs-script', 'hubsAjax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('hubs_nonce')
        ));
    }

    public function add_admin_menu() 
    {
        add_options_page(
            'ComunicarSe Hubs',
            'Hubs Widget',
            'manage_options',
            'comunicarse-hubs',
            array($this, 'admin_page')
        );
    }

    public function admin_page() 
    {
        ?>
        <div class="wrap">
            <h1>Configuración Hubs ComunicarSe</h1>
            <div id="hubs-admin-container">
                <div class="hubs-preview">
                    <h3>Vista Previa</h3>
                    <div id="hubs-preview-container">
                        <?php echo $this->render_hubs(); ?>
                    </div>
                </div>

                <div class="hubs-configuration">
                    <h3>Configuración</h3>
                    <form id="hubs-config-form">
                        <?php wp_nonce_field('hubs_nonce', 'hubs_nonce'); ?>
                        <div id="hubs-config-fields"></div>
                        <button type="submit" class="button button-primary">Guardar Configuración</button>
                        <button type="button" class="button" id="reset-hubs">Restaurar Predeterminado</button>
                    </form>
                </div>
            </div>

            <div class="hubs-usage">
                <h3>Cómo usar</h3>
                <p><strong>Shortcode:</strong> <code>[comunicarse_hubs]</code></p>
                <p><strong>PHP:</strong> <code>&lt;?php echo do_shortcode('[comunicarse_hubs]'); ?&gt;</code></p>
                <p><strong>Widget:</strong> Disponible en Apariencia > Widgets</p>
            </div>
        </div>

        <style>
        #hubs-admin-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin: 20px 0;
        }
        .hubs-preview, .hubs-configuration {
            background: white;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
        }
        .hub-config-item {
            border: 1px solid #ddd;
            padding: 15px;
            margin: 10px 0;
            border-radius: 5px;
        }
        .hub-config-item h4 {
            margin-top: 0;
            color: #667eea;
        }
        .hub-config-item input, .hub-config-item textarea {
            width: 100%;
            margin: 5px 0;
            padding: 8px;
        }
        .hubs-usage {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
        }
        </style>

        <script>
        jQuery(document).ready(function($) {
            loadHubsConfiguration();
            
            $('#hubs-config-form').on('submit', function(e) {
                e.preventDefault();
                saveHubsConfiguration();
            });

            $('#reset-hubs').on('click', function() {
                if (confirm('¿Restaurar configuración predeterminada?')) {
                    resetHubsConfiguration();
                }
            });

            function loadHubsConfiguration() {
                $.post(hubsAjax.ajax_url, {
                    action: 'get_hubs_config',
                    nonce: hubsAjax.nonce
                }, function(response) {
                    if (response.success) {
                        renderConfigFields(response.data);
                    }
                });
            }

            function saveHubsConfiguration() {
                const hubs = [];
                $('.hub-config-item').each(function() {
                    const hubId = $(this).data('hub-id');
                    hubs.push({
                        id: hubId,
                        title: $(this).find('.hub-title').val(),
                        description: $(this).find('.hub-description').val(),
                        url: $(this).find('.hub-url').val(),
                        icon: $(this).find('.hub-icon').val(),
                        image: $(this).find('.hub-image').val()
                    });
                });

                $.post(hubsAjax.ajax_url, {
                    action: 'save_hubs_config',
                    nonce: hubsAjax.nonce,
                    hubs: JSON.stringify(hubs)
                }, function(response) {
                    if (response.success) {
                        alert('Configuración guardada exitosamente');
                        location.reload();
                    } else {
                        alert('Error al guardar la configuración');
                    }
                });
            }

            function resetHubsConfiguration() {
                $.post(hubsAjax.ajax_url, {
                    action: 'save_hubs_config',
                    nonce: hubsAjax.nonce,
                    reset: true
                }, function(response) {
                    if (response.success) {
                        location.reload();
                    }
                });
            }

            function renderConfigFields(hubs) {
                const container = $('#hubs-config-fields');
                container.empty();

                hubs.forEach(function(hub) {
                    const configItem = $(`
                        <div class="hub-config-item" data-hub-id="${hub.id}">
                            <h4>Hub ${hub.id}: ${hub.title}</h4>
                            <label>Título:</label>
                            <input type="text" class="hub-title" value="${hub.title}">
                            
                            <label>Descripción:</label>
                            <textarea class="hub-description">${hub.description}</textarea>
                            
                            <label>URL:</label>
                            <input type="text" class="hub-url" value="${hub.url}">
                            
                            <label>Icono (emoji):</label>
                            <input type="text" class="hub-icon" value="${hub.icon}">
                            
                            <label>Imagen URL:</label>
                            <input type="text" class="hub-image" value="${hub.image || ''}">
                        </div>
                    `);
                    container.append(configItem);
                });
            }
        });
        </script>
        <?php
    }

    public function save_hubs_config() 
    {
        check_ajax_referer('hubs_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_die('No tienes permisos para realizar esta acción');
        }

        if (isset($_POST['reset'])) {
            delete_option('comunicarse_hubs_config');
            wp_send_json_success();
            return;
        }

        $hubs = json_decode(stripslashes($_POST['hubs']), true);
        
        if (is_array($hubs)) {
            update_option('comunicarse_hubs_config', $hubs);
            wp_send_json_success();
        } else {
            wp_send_json_error('Datos inválidos');
        }
    }

    public function get_hubs_config() 
    {
        check_ajax_referer('hubs_nonce', 'nonce');

        $config = get_option('comunicarse_hubs_config', $this->get_default_hubs());
        wp_send_json_success($config);
    }

    private function get_default_hubs() 
    {
        return array(
            array(
                'id' => 1,
                'title' => 'Gobierno Corporativo',
                'description' => 'Prácticas de transparencia, ética empresarial y estructuras de gobierno',
                'icon' => '🏛️',
                'url' => '/categoria/gobierno-corporativo',
                'image' => ''
            ),
            array(
                'id' => 2,
                'title' => 'Cambio Climático',
                'description' => 'Estrategias de mitigación, adaptación y descarbonización empresarial',
                'icon' => '🌍',
                'url' => '/categoria/cambio-climatico',
                'image' => ''
            ),
            array(
                'id' => 3,
                'title' => 'Economía Circular',
                'description' => 'Modelos de negocio sostenibles y gestión eficiente de recursos',
                'icon' => '♻️',
                'url' => '/categoria/economia-circular',
                'image' => ''
            ),
            array(
                'id' => 4,
                'title' => 'Diversidad e Inclusión',
                'description' => 'Equidad de género, inclusión laboral y derechos humanos',
                'icon' => '🤝',
                'url' => '/categoria/diversidad-inclusion',
                'image' => ''
            ),
            array(
                'id' => 5,
                'title' => 'Tecnología e Innovación',
                'description' => 'Innovación sostenible, tecnologías verdes y transformación digital',
                'icon' => '💡',
                'url' => '/categoria/tecnologia-innovacion',
                'image' => ''
            ),
            array(
                'id' => 6,
                'title' => 'Impacto Social',
                'description' => 'Inversión de impacto, negocios inclusivos y desarrollo comunitario',
                'icon' => '❤️',
                'url' => '/categoria/impacto-social',
                'image' => ''
            )
        );
    }

    public function render_hubs_shortcode($atts = array()) 
    {
        $atts = shortcode_atts(array(
            'class' => '',
            'show_title' => 'true'
        ), $atts);

        return $this->render_hubs($atts);
    }

    public function render_hubs($atts = array()) 
    {
        $hubs = get_option('comunicarse_hubs_config', $this->get_default_hubs());
        $show_title = isset($atts['show_title']) ? $atts['show_title'] === 'true' : true;
        $extra_class = isset($atts['class']) ? ' ' . esc_attr($atts['class']) : '';

        ob_start();
        ?>
        <div class="comunicarse-hubs-widget<?php echo $extra_class; ?>">
            <?php if ($show_title): ?>
            <div class="hubs-header">
                <h2 class="hubs-title">🌐 Hubs ComunicarSe</h2>
                <p class="hubs-subtitle">Centros temáticos de sostenibilidad empresarial</p>
            </div>
            <?php endif; ?>

            <div class="hubs-grid">
                <?php foreach ($hubs as $hub): ?>
                <a href="<?php echo esc_url($hub['url']); ?>" class="hub-card">
                    <div class="hub-content">
                        <div class="hub-image">
                            <?php if (!empty($hub['image'])): ?>
                                <img src="<?php echo esc_url($hub['image']); ?>" alt="<?php echo esc_attr($hub['title']); ?>">
                            <?php else: ?>
                                <span class="hub-icon"><?php echo esc_html($hub['icon']); ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="hub-info">
                            <h3 class="hub-title"><?php echo esc_html($hub['title']); ?></h3>
                            <p class="hub-description"><?php echo esc_html($hub['description']); ?></p>
                        </div>
                        <div class="hub-arrow">→</div>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    public function register_widget() 
    {
        register_widget('ComunicarSe_Hubs_Widget');
    }

    private function create_table() 
    {
        // No necesitamos tabla para este plugin simple
        // Los datos se guardan en wp_options
    }
}

/**
 * Widget class
 */
class ComunicarSe_Hubs_Widget extends WP_Widget 
{
    public function __construct() 
    {
        parent::__construct(
            'comunicarse_hubs_widget',
            'ComunicarSe Hubs',
            array('description' => 'Widget de hubs temáticos configurables')
        );
    }

    public function widget($args, $instance) 
    {
        echo $args['before_widget'];

        if (!empty($instance['title'])) {
            echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
        }

        $plugin = new ComunicarSeHubsPlugin();
        echo $plugin->render_hubs(array('show_title' => false));

        echo $args['after_widget'];
    }

    public function form($instance) 
    {
        $title = !empty($instance['title']) ? $instance['title'] : 'Hubs ComunicarSe';
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>">Título:</label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" 
                   name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" 
                   value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <small>Configura los hubs en <strong>Ajustes > Hubs Widget</strong></small>
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) 
    {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? sanitize_text_field($new_instance['title']) : '';
        return $instance;
    }
}

// Inicializar el plugin
new ComunicarSeHubsPlugin();

// Hook para activación del plugin
register_activation_hook(__FILE__, 'comunicarse_hubs_activate');
function comunicarse_hubs_activate() 
{
    // Crear configuración predeterminada si no existe
    if (!get_option('comunicarse_hubs_config')) {
        $plugin = new ComunicarSeHubsPlugin();
        // La configuración se creará automáticamente cuando se solicite
    }
}

// Hook para desactivación del plugin
register_deactivation_hook(__FILE__, 'comunicarse_hubs_deactivate');
function comunicarse_hubs_deactivate() 
{
    // Limpiar cache si es necesario
    wp_cache_flush();
}
?>