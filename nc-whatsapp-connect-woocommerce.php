<?php
/**
 * Plugin Name: NC WhatsApp Connect for WooCommerce
 * Plugin URI: https://nordecode.com.br/plugins/nc-whatsapp-connect-woocommerce
 * Description: Adiciona um botão flutuante do WhatsApp com mensagens personalizadas e integração automática com produtos do WooCommerce. Melhore seu atendimento com comunicação direta entre clientes e vendedores.
 * Version: 1.0.3
 * Requires at least: 5.8
 * Requires PHP: 7.2
 * Author: Nordecode
 * Author URI: https://nordecode.com.br
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: nc-whatsapp-connect
 * Domain Path: /languages
 * WC requires at least: 5.0
 * WC tested up to: 8.0
 */

if (!defined('ABSPATH')) {
    exit;
}

// Define constantes do plugin
define('NC_WHATSAPP_CONNECT_VERSION', '1.0.0');
define('NC_WHATSAPP_CONNECT_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('NC_WHATSAPP_CONNECT_PLUGIN_URL', plugin_dir_url(__FILE__));

// Carrega a classe principal
require_once NC_WHATSAPP_CONNECT_PLUGIN_DIR . 'includes/class-nc-whatsapp-connect.php';

// Ativa o plugin
function nc_whatsapp_connect_activate() {
    add_option('nc_whatsapp_connect_version', NC_WHATSAPP_CONNECT_VERSION);
}
register_activation_hook(__FILE__, 'nc_whatsapp_connect_activate');

// Desativa o plugin
function nc_whatsapp_connect_deactivate() {
    // Código de desativação
}
register_deactivation_hook(__FILE__, 'nc_whatsapp_connect_deactivate');

// Inicia o plugin
function nc_whatsapp_connect_init() {
    if (!class_exists('WooCommerce')) {
        add_action('admin_notices', 'nc_whatsapp_connect_wc_missing_notice');
        return;
    }

    load_plugin_textdomain('nc-whatsapp-connect', false, dirname(plugin_basename(__FILE__)) . '/languages/');
    NC_WhatsApp_Connect::get_instance();
}
add_action('plugins_loaded', 'nc_whatsapp_connect_init');

function nc_whatsapp_connect_wc_missing_notice() {
    ?>
    <div class="error">
        <p><?php _e('NC WhatsApp Connect requer que o WooCommerce esteja instalado e ativo.', 'nc-whatsapp-connect'); ?></p>
    </div>
    <?php
}