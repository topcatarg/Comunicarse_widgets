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

/**
 * Clase principal del plugin
 */
class ComunicarSeHubsPlugin 
{
    public function __construct() 
    {
        add_action('init', array($this, 'init'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
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
    }

    public function enqueue_scripts() 
    {
        wp_enqueue_style(
            'comunicarse-hubs-style',
            COMUNICARSE_HUBS_PLUGIN_URL . 'assets/style.css',
            array(),
            COMUNICARSE_HUBS_VERSION
        );
    }

    public function enqueue_admin_scripts($hook) 
    {
        if ($hook !== 'settings_page_comunicarse-hubs') {
            return;
        }

        wp_enqueue_script('jquery');
        
        wp_enqueue_script(
            'comunicarse-hubs-admin',
            COMUNICARSE_HUBS_PLUGIN_URL . 'assets/admin.js',
            array('jquery'),
            COMUNICARSE_HUBS_VERSION,
            true
        );

        wp_localize_script('comunicarse-hubs-admin', 'hubsAjax', array(
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
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin: 20px 0;">
                <div style="background: white; padding: 20px; border: 1px solid #ddd; border-radius: 8px;">
                    <h3>Vista Previa</h3>
                    <div id="hubs-preview-container">
                        <?php echo $this->render_hubs(array('show_title' => false)); ?>
                    </div>
                </div>

                <div style="background: white; padding: 20px; border: 1px solid #ddd; border-radius: 8px;">
                    <h3>Configuración</h3>
                    <form id="hubs-config-form">
                        <?php wp_nonce_field('hubs_nonce', 'hubs_nonce'); ?>
                        <div id="hubs-config-fields"></div>
                        <p>
                            <button type="submit" class="button button-primary">Guardar Configuración</button>
                            <button type="button" class="button" id="reset-hubs">Restaurar Predeterminado</button>
                        </p>
                    </form>
                </div>
            </div>

            <div style="background: #f9f9f9; padding: 20px; border-radius: 8px;">
                <h3>Cómo usar</h3>
                <p><strong>Shortcode:</strong> <code>[comunicarse_hubs]</code></p>
                <p><strong>PHP:</strong> <code>&lt;?php echo do_shortcode('[comunicarse_hubs]'); ?&gt;</code></p>
                <p><strong>Widget:</strong> Disponible en Apariencia > Widgets</p>
            </div>
        </div>

        <style>
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
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .hub-config-item label {
            display: block;
            font-weight: bold;
            margin-top: 10px;
        }
        </style>
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
                        </div>
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
    // Forzar la creación de la configuración predeterminada
    if (!get_option('comunicarse_hubs_config')) {
        $plugin = new ComunicarSeHubsPlugin();
        $default_hubs = array(
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
        update_option('comunicarse_hubs_config', $default_hubs);
    }
}
?>