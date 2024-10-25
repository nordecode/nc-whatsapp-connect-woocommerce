<?php
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Remove todas as opções do banco de dados
delete_option('nc_whatsapp_connect_phone');
delete_option('nc_whatsapp_connect_message');
delete_option('nc_whatsapp_connect_position');
delete_option('nc_whatsapp_connect_color');
delete_option('nc_whatsapp_connect_show_mobile');
delete_option('nc_whatsapp_connect_product_message');
delete_option('nc_whatsapp_connect_version');

delete_option('nc_whatsapp_connect_show_support_text');
delete_option('nc_whatsapp_connect_support_text');
delete_option('nc_whatsapp_connect_show_hours');
delete_option('nc_whatsapp_connect_hours_text');