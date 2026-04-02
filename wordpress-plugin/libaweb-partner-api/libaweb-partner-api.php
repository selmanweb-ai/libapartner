<?php
/**
 * Plugin Name: LibaWeb Partner API
 * Description: Liba Partner Mobil Uygulaması için özel JWT ve WooCommerce/Form Endpoint Entegrasyonu.
 * Version: 1.0.0
 * Author: LibaWeb
 * Text Domain: libaweb-api
 */

if (!defined('ABSPATH')) {
    exit; // Doğrudan erişimi engelle
}

// 1. Temel JWT Sınıfını Dahil Et
require_once plugin_dir_path(__FILE__) . 'inc/class-jwt.php';

// 2. Form Tarayıcı (Flamingo/WPForms) Sınıfını Dahil Et
require_once plugin_dir_path(__FILE__) . 'inc/class-form-manager.php';

// 3. API Uçları Sınıfını Dahil Et
require_once plugin_dir_path(__FILE__) . 'inc/class-api-endpoints.php';

// 4. Eklenti Aktifleştirildiğinde yapılacaklar:
register_activation_hook(__FILE__, 'libaweb_api_activate');
function libaweb_api_activate() {
    // Kurulumda JWT Secret Key'in wp-config'de var olup olmadığını kontrol et
    if (!defined('LIBAWEB_JWT_SECRET')) {
        // Eğer config'de yoksa, DB'ye rastgele bir key at
        if (!get_option('libaweb_jwt_secret')) {
            update_option('libaweb_jwt_secret', wp_generate_password(64, true, true));
        }
    }
}

// 5. REST API Başlatıcısı
add_action('rest_api_init', function () {
    $api = new LibaWeb_API_Endpoints();
    $api->register_routes();
});

// 6. Hooklar (Yeni Sipariş ve Yeni Form Bildirimleri İçin)
add_action('woocommerce_new_order', 'libaweb_notify_new_order', 10, 2);
function libaweb_notify_new_order($order_id, $order) {
    // Yeni bir sipariş geldiğinde Firebase FCM'ye push atan fonksiyon...
    // Bu fonksiyona daha sonra FCM kodlarını bağlayacağız.
}

// İletişim Formları için dinamik hook örnekleri (Örn: wpcf7_mail_sent, wpforms_process_complete)
add_action('wpcf7_mail_sent', 'libaweb_notify_new_cf7_form', 10, 1);
function libaweb_notify_new_cf7_form($contact_form) {
    // CF7 Formu başarıyla gönderildiğinde bildirimi tetikler
}

add_action('wpforms_process_complete', 'libaweb_notify_new_wpform', 10, 4);
function libaweb_notify_new_wpform($fields, $entry, $form_data, $entry_id) {
    // WPForms Formu başarıyla gönderildiğinde bildirimi tetikler
}
