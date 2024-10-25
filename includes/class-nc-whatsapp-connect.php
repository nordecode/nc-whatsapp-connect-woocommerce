<?php
if (!defined('ABSPATH')) {
    exit;
}

class NC_WhatsApp_Connect {
    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('wp_footer', array($this, 'add_whatsapp_button'));
        
        // Adiciona suporte a tradução
        add_action('init', array($this, 'load_textdomain'));
    }

    public function load_textdomain() {
        load_plugin_textdomain('nc-whatsapp-connect', false, dirname(plugin_basename(__FILE__)) . '/languages');
    }

    public function add_admin_menu() {
        add_menu_page(
            __('NC WhatsApp Connect', 'nc-whatsapp-connect'),
            __('WhatsApp Connect', 'nc-whatsapp-connect'),
            'manage_options',
            'nc-whatsapp-connect',
            array($this, 'admin_page'),
            'dashicons-whatsapp',
            56
        );
    }

    public function register_settings() {
        register_setting('nc_whatsapp_connect_options', 'nc_whatsapp_connect_phone', array(
            'type' => 'string',
            'sanitize_callback' => array($this, 'sanitize_phone')
        ));
        register_setting('nc_whatsapp_connect_options', 'nc_whatsapp_connect_message');
        register_setting('nc_whatsapp_connect_options', 'nc_whatsapp_connect_position');
        register_setting('nc_whatsapp_connect_options', 'nc_whatsapp_connect_color');
        register_setting('nc_whatsapp_connect_options', 'nc_whatsapp_connect_show_mobile');
        register_setting('nc_whatsapp_connect_options', 'nc_whatsapp_connect_product_message');
        
        // Novas configurações
        register_setting('nc_whatsapp_connect_options', 'nc_whatsapp_connect_show_support_text');
        register_setting('nc_whatsapp_connect_options', 'nc_whatsapp_connect_support_text');
        register_setting('nc_whatsapp_connect_options', 'nc_whatsapp_connect_show_hours');
        register_setting('nc_whatsapp_connect_options', 'nc_whatsapp_connect_hours_text');
    }

    public function sanitize_phone($phone) {
        return preg_replace('/[^0-9]/', '', $phone);
    }

    public function admin_page() {
        ?>
        <div class="wrap">
            <h2><?php _e('NC WhatsApp Connect - Configurações', 'nc-whatsapp-connect'); ?></h2>
            <form method="post" action="options.php" class="nc-settings-form">
                <?php
                settings_fields('nc_whatsapp_connect_options');
                do_settings_sections('nc_whatsapp_connect_options');
                ?>
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php _e('Número do WhatsApp', 'nc-whatsapp-connect'); ?></th>
                        <td>
                            <input type="text" name="nc_whatsapp_connect_phone" 
                                value="<?php echo esc_attr(get_option('nc_whatsapp_connect_phone')); ?>" 
                                class="regular-text nc-phone-input"
                                placeholder="5511999999999"
                            />
                            <p class="description"><?php _e('Digite o número no formato internacional (ex: 5511999999999)', 'nc-whatsapp-connect'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Mensagem Padrão', 'nc-whatsapp-connect'); ?></th>
                        <td>
                            <textarea name="nc_whatsapp_connect_message" 
                                rows="3" 
                                class="large-text nc-message-input"
                            ><?php echo esc_textarea(get_option('nc_whatsapp_connect_message', __('Olá! Vim pelo seu site e gostaria de mais informações.', 'nc-whatsapp-connect'))); ?></textarea>
                            <p class="description"><?php _e('Mensagem padrão que será enviada quando o cliente clicar no botão', 'nc-whatsapp-connect'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Mensagem para Produtos', 'nc-whatsapp-connect'); ?></th>
                        <td>
                            <textarea name="nc_whatsapp_connect_product_message" 
                                rows="3" 
                                class="large-text nc-product-message-input"
                            ><?php echo esc_textarea(get_option('nc_whatsapp_connect_product_message', __('Olá! Gostaria de mais informações sobre o produto {product_name} ({product_url})', 'nc-whatsapp-connect'))); ?></textarea>
                            <p class="description"><?php _e('Use {product_name}, {product_price}, {product_url} como variáveis', 'nc-whatsapp-connect'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Posição do Botão', 'nc-whatsapp-connect'); ?></th>
                        <td>
                            <select name="nc_whatsapp_connect_position" class="nc-position-select">
                                <option value="right" <?php selected(get_option('nc_whatsapp_connect_position'), 'right'); ?>><?php _e('Direita', 'nc-whatsapp-connect'); ?></option>
                                <option value="left" <?php selected(get_option('nc_whatsapp_connect_position'), 'left'); ?>><?php _e('Esquerda', 'nc-whatsapp-connect'); ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Cor do Botão', 'nc-whatsapp-connect'); ?></th>
                        <td>
                            <input type="color" 
                                name="nc_whatsapp_connect_color" 
                                value="<?php echo esc_attr(get_option('nc_whatsapp_connect_color', '#25D366')); ?>" 
                                class="nc-color-picker"
                            />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Exibir no Mobile', 'nc-whatsapp-connect'); ?></th>
                        <td>
                            <input type="checkbox" 
                                name="nc_whatsapp_connect_show_mobile" 
                                value="1" 
                                <?php checked(get_option('nc_whatsapp_connect_show_mobile', '1'), 1); ?> 
                                class="nc-mobile-checkbox"
                            />
                        </td>
                    </tr>
                    <!-- Novas opções -->
                    <tr>
                        <th scope="row"><?php _e('Exibir Texto de Suporte', 'nc-whatsapp-connect'); ?></th>
                        <td>
                            <input type="checkbox" 
                                name="nc_whatsapp_connect_show_support_text" 
                                value="1" 
                                <?php checked(get_option('nc_whatsapp_connect_show_support_text', '1'), 1); ?> 
                            />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Texto de Suporte', 'nc-whatsapp-connect'); ?></th>
                        <td>
                            <input type="text" 
                                name="nc_whatsapp_connect_support_text" 
                                value="<?php echo esc_attr(get_option('nc_whatsapp_connect_support_text', 'Nossa equipe está pronta para te atender! 💬')); ?>" 
                                class="regular-text"
                            />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Exibir Horário de Atendimento', 'nc-whatsapp-connect'); ?></th>
                        <td>
                            <input type="checkbox" 
                                name="nc_whatsapp_connect_show_hours" 
                                value="1" 
                                <?php checked(get_option('nc_whatsapp_connect_show_hours', '1'), 1); ?> 
                            />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Texto do Horário', 'nc-whatsapp-connect'); ?></th>
                        <td>
                            <input type="text" 
                                name="nc_whatsapp_connect_hours_text" 
                                value="<?php echo esc_attr(get_option('nc_whatsapp_connect_hours_text', 'Horário de atendimento: Segunda a Sexta, 9h às 18h')); ?>" 
                                class="regular-text"
                            />
                        </td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }

    public function enqueue_scripts() {
        if (!class_exists('WooCommerce')) {
            return;
        }

        wp_enqueue_style(
            'nc-whatsapp-connect-style',
            NC_WHATSAPP_CONNECT_PLUGIN_URL . 'assets/css/whatsapp-button.css',
            array(),
            NC_WHATSAPP_CONNECT_VERSION
        );

        wp_enqueue_script(
            'nc-whatsapp-connect-script',
            NC_WHATSAPP_CONNECT_PLUGIN_URL . 'assets/js/whatsapp-button.js',
            array('jquery'),
            NC_WHATSAPP_CONNECT_VERSION,
            true
        );

        $product_data = $this->get_product_data();
        
        wp_localize_script('nc-whatsapp-connect-script', 'whatsappConnectVars', array(
            'phone' => get_option('nc_whatsapp_connect_phone'),
            'message' => get_option('nc_whatsapp_connect_message'),
            'position' => get_option('nc_whatsapp_connect_position', 'right'),
            'color' => get_option('nc_whatsapp_connect_color', '#25D366'),
            'showMobile' => get_option('nc_whatsapp_connect_show_mobile', '1'),
            'productMessage' => get_option('nc_whatsapp_connect_product_message'),
            'isProduct' => is_product() && $product_data !== null,
            'productData' => $product_data,
            // Novas opções
            'showSupportText' => get_option('nc_whatsapp_connect_show_support_text', '1'),
            'supportText' => get_option('nc_whatsapp_connect_support_text', 'Nossa equipe está pronta para te atender! 💬'),
            'showHours' => get_option('nc_whatsapp_connect_show_hours', '1'),
            'hoursText' => get_option('nc_whatsapp_connect_hours_text', 'Horário de atendimento: Segunda a Sexta, 9h às 18h'),
            'i18n' => array(
                'sendMessage' => __('Enviar mensagem', 'nc-whatsapp-connect'),
                'startChat' => __('Iniciar conversa', 'nc-whatsapp-connect')
            )
        ));
    }

    private function get_product_data() {
        if (!is_product() || !function_exists('wc_get_product')) {
            return null;
        }

        try {
            $product = wc_get_product(get_the_ID());
            
            if (!$product instanceof WC_Product) {
                return null;
            }

            return array(
                'name' => wp_strip_all_tags($product->get_name()),
                'price' => wp_strip_all_tags(wc_price($product->get_price())),
                'url' => esc_url(get_permalink($product->get_id()))
            );
        } catch (Exception $e) {
            error_log('NC WhatsApp Connect - Erro ao obter dados do produto: ' . $e->getMessage());
            return null;
        }
    }

    public function add_whatsapp_button() {
        $vars = array(
            'phone' => get_option('nc_whatsapp_connect_phone'),
            'position' => get_option('nc_whatsapp_connect_position', 'right'),
            'color' => get_option('nc_whatsapp_connect_color', '#25D366'),
            'showMobile' => get_option('nc_whatsapp_connect_show_mobile', '1'),
            'showSupportText' => get_option('nc_whatsapp_connect_show_support_text', '1'),
            'supportText' => get_option('nc_whatsapp_connect_support_text', 'Nossa equipe está pronta para te atender! 💬'),
            'showHours' => get_option('nc_whatsapp_connect_show_hours', '1'),
            'hoursText' => get_option('nc_whatsapp_connect_hours_text', 'Horário de atendimento: Segunda a Sexta, 9h às 18h'),
        );
        ?>
        <div id="nc-whatsapp-connect">
            <div class="nc-whatsapp-btn nc-<?php echo esc_attr($vars['position']); ?> <?php echo !$vars['showMobile'] ? 'nc-hide-mobile' : ''; ?>" >
                <div class="nc-whatsapp-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="100%" height="100%" preserveAspectRatio="xMidYMid meet">
                        <path fill="#25d366" d="M12 0C5.373 0 0 5.373 0 12c0 6.627 5.373 12 12 12s12-5.373 12-12c0-6.627-5.373-12-12-12z"></path>
                        <path fill="#ffffff" d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86s.274.072.376-.043c.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.045.072.045.419-.1.824z"></path>
                    </svg>
                </div>
                <div class="nc-whatsapp-popup">
                    <div class="nc-popup-header">
                        <div class="nc-header-content">
                            <img src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyNCIgaGVpZ2h0PSIyNCIgdmlld0JveD0iMCAwIDI0IDI0IiBmaWxsPSJub25lIiBzdHJva2U9IiMyNWQzNjYiIHN0cm9rZS13aWR0aD0iMiIgc3Ryb2tlLWxpbmVjYXA9InJvdW5kIiBzdHJva2UtbGluZWpvaW49InJvdW5kIj48cGF0aCBkPSJNMjIgMTEuMDhWMTJhMTAgMTAgMCAxIDEtNS45My05LjE0Ij48L3BhdGg+PHBvbHlsaW5lIHBvaW50cz0iMjIgNCAxMiAxNC4wMSA5IDExLjAxIj48L3BvbHlsaW5lPjwvc3ZnPg==" alt="Verificado" class="nc-verified-icon"/>
                            <span><?php _e('Atendimento via WhatsApp', 'nc-whatsapp-connect'); ?></span>
                        </div>
                        <button class="nc-close-popup" aria-label="<?php _e('Fechar', 'nc-whatsapp-connect'); ?>">&times;</button>
                    </div>
                    <div class="nc-popup-content">
                        <?php if ($vars['showSupportText'] || $vars['showHours']) : ?>
                            <div class="nc-support-info">
                                <?php if ($vars['showSupportText']) : ?>
                                    <p class="nc-support-text"><?php echo esc_html($vars['supportText']); ?></p>
                                <?php endif; ?>
                                <?php if ($vars['showHours']) : ?>
                                    <p class="nc-support-hours"><?php echo esc_html($vars['hoursText']); ?></p>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        <div class="nc-message-area">
                            <label for="whatsapp-message" class="nc-message-label"><?php _e('Sua mensagem:', 'nc-whatsapp-connect'); ?></label>
                            <textarea 
                                id="whatsapp-message" 
                                class="nc-message-input" 
                                rows="4" 
                                placeholder="<?php _e('Digite sua mensagem aqui...', 'nc-whatsapp-connect'); ?>"
                            ></textarea>
                        </div>
                        <button class="nc-send-message">
                            <svg class="nc-whatsapp-send-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 2L11 13"></path>
                                <path d="M22 2L15 22L11 13L2 9L22 2z"></path>
                            </svg>
                            <?php _e('Iniciar conversa', 'nc-whatsapp-connect'); ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}