<?php
/**
 * Plugin Name: Código Nativo Connect
 * Plugin URI: https://codigonativo.com
 * Description: Plugin para conectar WordPress con el sistema de gestión de Código Nativo
 * Version: 1.0.0
 * Author: Código Nativo
 * Author URI: https://codigonativo.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: codigo-nativo-connect
 * Domain Path: /languages
 */

// Evitar acceso directo al archivo
if (!defined('ABSPATH')) {
    exit;
}

// Definir constantes del plugin
define('CN_PLUGIN_VERSION', '1.0.0');
define('CN_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('CN_PLUGIN_URL', plugin_dir_url(__FILE__));
define('CN_PLUGIN_FILE', __FILE__);

// Clase principal del plugin
class CodigoNativoConnect {
    
    private static $instance = null;
    
    /**
     * Obtener instancia singleton
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor
     */
    private function __construct() {
        // Procesar formulario
        if (isset($_POST['cn_token_nonce']) && wp_verify_nonce($_POST['cn_token_nonce'], 'cn_save_token')) {
            if (current_user_can('manage_options')) {
                if (isset($_POST['cn_clear_token'])) {
                    delete_option('cn_api_token');
                    add_action('admin_notices', function() {
                        echo '<div class="notice notice-success"><p>Token eliminado correctamente.</p></div>';
                    });
                } elseif (isset($_POST['cn_api_token'])) {
                    $token = sanitize_text_field($_POST['cn_api_token']);
                    if (!empty($token)) {
                        update_option('cn_api_token', $token);
                        add_action('admin_notices', function() {
                            echo '<div class="notice notice-success"><p>Token guardado correctamente. La conexión está activa.</p></div>';
                        });
                    }
                }
            }
        }
        
        $this->init_hooks();
    }
    
    /**
     * Inicializar hooks de WordPress
     */
    private function init_hooks() {
        // Hooks de activación/desactivación
        register_activation_hook(CN_PLUGIN_FILE, array($this, 'activate'));
        register_deactivation_hook(CN_PLUGIN_FILE, array($this, 'deactivate'));
        
        // Agregar menú de administración
        add_action('admin_menu', array($this, 'add_admin_menu'));
        
        // Registrar API REST
        add_action('rest_api_init', array($this, 'register_api_routes'));
        
        // Cargar scripts de administración
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        
        // Habilitar CORS para la API REST
        add_action('rest_api_init', array($this, 'enable_cors'));
    }
    
    /**
     * Habilitar CORS para peticiones API
     */
    public function enable_cors() {
        remove_filter('rest_pre_serve_request', 'rest_send_cors_headers');
        add_filter('rest_pre_serve_request', function($value) {
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
            header('Access-Control-Allow-Headers: Content-Type, Authorization, X-CN-Token');
            header('Access-Control-Allow-Credentials: true');
            
            if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
                status_header(200);
                exit;
            }
            
            return $value;
        });
    }
    
    /**
     * Activación del plugin
     */
    public function activate() {
        // Guardar versión del plugin
        update_option('cn_plugin_version', CN_PLUGIN_VERSION);
        
        // Flush rewrite rules para las rutas API
        flush_rewrite_rules();
    }
    
    /**
     * Desactivación del plugin
     */
    public function deactivate() {
        flush_rewrite_rules();
    }
    
    /**
     * Agregar menú de administración
     */
    public function add_admin_menu() {
        add_menu_page(
            'Código Nativo',
            'Código Nativo',
            'manage_options',
            'codigo-nativo',
            array($this, 'render_admin_page'),
            'dashicons-admin-site-alt3',
            100
        );
    }
    
    /**
     * Renderizar página de administración
     */
    public function render_admin_page() {
        $token = get_option('cn_api_token', '');
        $site_url = get_site_url();
        $is_connected = !empty($token);
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            
            <div class="card">
                <h2>Configuración de conexión</h2>
                <p>Para conectar este sitio WordPress con el dashboard de Código Nativo:</p>
                <ol>
                    <li>Ve al dashboard y crea un nuevo proyecto WordPress</li>
                    <li>El dashboard generará un token automáticamente</li>
                    <li>Copia ese token y pégalo aquí abajo</li>
                    <li>Haz clic en "Guardar y Validar"</li>
                </ol>
                
                <form method="post" action="">
                    <?php wp_nonce_field('cn_save_token', 'cn_token_nonce'); ?>
                    
                    <table class="form-table">
                        <tr>
                            <th scope="row">URL del sitio:</th>
                            <td>
                                <code><?php echo esc_html($site_url); ?></code>
                                <p class="description">Usa esta URL en el dashboard al crear el proyecto</p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="cn-token-field">Token del Dashboard:</label>
                            </th>
                            <td>
                                <input type="text" 
                                       name="cn_api_token" 
                                       id="cn-token-field" 
                                       value="<?php echo esc_attr($token); ?>" 
                                       class="regular-text" 
                                       placeholder="Pega aquí el token generado en el dashboard" 
                                       <?php echo $is_connected ? '' : 'required'; ?> />
                                <?php if ($is_connected): ?>
                                    <span class="cn-status-badge connected">✓ Conectado</span>
                                <?php else: ?>
                                    <span class="cn-status-badge disconnected">✗ No conectado</span>
                                <?php endif; ?>
                                <p class="description">Token de 32 caracteres generado desde el dashboard</p>
                            </td>
                        </tr>
                    </table>
                    
                    <div class="cn-actions">
                        <button type="submit" class="button button-primary">
                            <span class="dashicons dashicons-yes"></span> Guardar y Validar
                        </button>
                        <?php if ($is_connected): ?>
                            <button type="button" id="cn-test-connection" class="button button-secondary">
                                <span class="dashicons dashicons-update"></span> Probar Conexión
                            </button>
                            <button type="submit" name="cn_clear_token" value="1" class="button button-link-delete">
                                <span class="dashicons dashicons-no"></span> Desconectar
                            </button>
                        <?php endif; ?>
                    </div>
                </form>
                
                <div id="cn-message" class="notice" style="display:none; margin-top: 20px;">
                    <p></p>
                </div>
            </div>
            
            <div class="card" style="margin-top: 20px;">
                <h2>Información del sistema</h2>
                <table class="widefat striped">
                    <tbody>
                        <tr>
                            <td><strong>Versión de WordPress:</strong></td>
                            <td><?php echo esc_html(get_bloginfo('version')); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Versión del Plugin:</strong></td>
                            <td><?php echo esc_html(CN_PLUGIN_VERSION); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Tema Activo:</strong></td>
                            <td><?php echo esc_html(wp_get_theme()->get('Name') . ' v' . wp_get_theme()->get('Version')); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Plugins Activos:</strong></td>
                            <td><?php echo esc_html(count(get_option('active_plugins'))); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <?php
    }
    
    /**
     * Cargar scripts de administración
     */
    public function enqueue_admin_scripts($hook) {
        // Solo cargar en nuestra página
        if ($hook !== 'toplevel_page_codigo-nativo') {
            return;
        }
        
        wp_enqueue_style(
            'cn-admin-styles',
            CN_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            CN_PLUGIN_VERSION
        );
        
        wp_enqueue_script(
            'cn-admin-script',
            CN_PLUGIN_URL . 'assets/js/admin.js',
            array('jquery'),
            CN_PLUGIN_VERSION,
            true
        );
        
        // Pasar datos al JavaScript
        wp_localize_script('cn-admin-script', 'cnData', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('cn_nonce'),
            'apiUrl' => rest_url('codigo-nativo/v1/')
        ));
    }
    
    /**
     * Registrar rutas API REST
     */
    public function register_api_routes() {
        // Ruta para validar token (GET para evitar bloqueos de ModSecurity)
        register_rest_route('codigo-nativo/v1', '/validate', array(
            'methods' => array('GET', 'POST', 'OPTIONS'),
            'callback' => array($this, 'api_validate_token'),
            'permission_callback' => '__return_true',
            'args' => array(
                'token' => array(
                    'required' => true,
                    'type' => 'string',
                    'sanitize_callback' => 'sanitize_text_field'
                )
            )
        ));
        
        // Ruta para obtener información de plugins
        register_rest_route('codigo-nativo/v1', '/plugins', array(
            'methods' => array('GET', 'OPTIONS'),
            'callback' => array($this, 'api_get_plugins'),
            'permission_callback' => array($this, 'check_api_permission')
        ));
        
        // Ruta para obtener información del sitio
        register_rest_route('codigo-nativo/v1', '/site-info', array(
            'methods' => array('GET', 'OPTIONS'),
            'callback' => array($this, 'api_get_site_info'),
            'permission_callback' => array($this, 'check_api_permission')
        ));
    }
    
    /**
     * Validar token de API
     */
    public function api_validate_token($request) {
        // Obtener token del query string
        $token = $request->get_param('token');
        $stored_token = get_option('cn_api_token');
        
        if (empty($token)) {
            return new WP_REST_Response(array(
                'success' => false,
                'message' => 'Token no proporcionado'
            ), 400);
        }
        
        if (empty($stored_token)) {
            return new WP_REST_Response(array(
                'success' => false,
                'message' => 'Plugin no configurado. Por favor, ingresa el token en la configuración del plugin.'
            ), 400);
        }
        
        // Usar hash_equals para prevenir timing attacks
        if (!hash_equals($stored_token, $token)) {
            return new WP_REST_Response(array(
                'success' => false,
                'message' => 'Token inválido'
            ), 401);
        }
        
        return new WP_REST_Response(array(
            'success' => true,
            'message' => 'Token válido',
            'site_url' => get_site_url(),
            'site_name' => get_bloginfo('name')
        ), 200);
    }
    
    /**
     * Obtener lista de plugins
     */
    public function api_get_plugins($request) {
        if (!function_exists('get_plugins')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        
        $all_plugins = get_plugins();
        $active_plugins = get_option('active_plugins');
        
        $plugins_data = array();
        
        foreach ($all_plugins as $plugin_file => $plugin_data) {
            $plugins_data[] = array(
                'name' => $plugin_data['Name'],
                'version' => $plugin_data['Version'],
                'author' => $plugin_data['Author'],
                'active' => in_array($plugin_file, $active_plugins),
                'file' => $plugin_file
            );
        }
        
        return new WP_REST_Response(array(
            'success' => true,
            'plugins' => $plugins_data
        ), 200);
    }
    
    /**
     * Obtener información del sitio
     */
    public function api_get_site_info($request) {
        $theme = wp_get_theme();
        
        return new WP_REST_Response(array(
            'success' => true,
            'site' => array(
                'name' => get_bloginfo('name'),
                'url' => get_site_url(),
                'description' => get_bloginfo('description'),
                'wp_version' => get_bloginfo('version'),
                'theme' => array(
                    'name' => $theme->get('Name'),
                    'version' => $theme->get('Version'),
                    'author' => $theme->get('Author')
                ),
                'plugins_count' => count(get_option('active_plugins')),
                'language' => get_bloginfo('language')
            )
        ), 200);
    }
    
    /**
     * Verificar permisos de API
     */
    public function check_api_permission($request) {
        $token = $request->get_header('X-CN-Token');
        
        if (empty($token)) {
            $token = $request->get_param('token');
        }
        
        $stored_token = get_option('cn_api_token');
        
        return hash_equals($stored_token, $token);
    }
}

// Inicializar el plugin
function codigo_nativo_connect_init() {
    return CodigoNativoConnect::get_instance();
}

// Iniciar el plugin
add_action('plugins_loaded', 'codigo_nativo_connect_init');

