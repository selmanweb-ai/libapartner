<?php
if (!defined('ABSPATH')) {
    exit;
}

class LibaWeb_API_Endpoints {

    public function register_routes() {
        // Kimlik Doğrulama Uçları
        register_rest_route('liba/v1', '/auth/token', [
            'methods' => 'POST',
            'callback' => [$this, 'generate_token'],
            'permission_callback' => '__return_true',
        ]);

        // Dashboard Verileri
        register_rest_route('liba/v1', '/dashboard/summary', [
            'methods' => 'GET',
            'callback' => [$this, 'get_dashboard_summary'],
            'permission_callback' => [$this, 'check_jwt_permission'],
        ]);

        // Siparişler
        register_rest_route('liba/v1', '/orders', [
            'methods' => 'GET',
            'callback' => [$this, 'get_orders'],
            'permission_callback' => [$this, 'check_jwt_permission'],
        ]);

        // Ürünler
        register_rest_route('liba/v1', '/products', [
            'methods' => 'GET',
            'callback' => [$this, 'get_products'],
            'permission_callback' => [$this, 'check_jwt_permission'],
        ]);
        
        // Randevular / Formlar
        register_rest_route('liba/v1', '/appointments', [
            'methods' => 'GET',
            'callback' => [$this, 'get_appointments'],
            'permission_callback' => [$this, 'check_jwt_permission'],
        ]);
    }

    public function check_jwt_permission(\WP_REST_Request $request) {
        $auth_header = $request->get_header('Authorization');
        if (!$auth_header || !preg_match('/Bearer\s(\S+)/', $auth_header, $matches)) {
            return new \WP_Error('jwt_auth_no_auth_header', 'Yetkilendirme başlığı bulunamadı.', ['status' => 403]);
        }

        $token = $matches[1];
        $user_id = LibaWeb_JWT::validate_token($token);

        if (!$user_id) {
            return new \WP_Error('jwt_auth_invalid_token', 'Geçersiz veya süresi dolmuş token.', ['status' => 403]);
        }

        // Kullanıcıyı oturum açmış gibi kabul et
        wp_set_current_user($user_id);
        return true;
    }

    public function generate_token(\WP_REST_Request $request) {
        $username = $request->get_param('username');
        $password = $request->get_param('password');

        $user = wp_authenticate($username, $password);

        if (is_wp_error($user)) {
            return new \WP_Error('jwt_auth_failed', 'Geçersiz kullanıcı adı veya şifre.', ['status' => 403]);
        }

        $token = LibaWeb_JWT::generate_token($user->ID);

        return rest_ensure_response([
            'token' => $token,
            'user_email' => $user->user_email,
            'user_display_name' => $user->display_name,
        ]);
    }

    public function get_dashboard_summary(\WP_REST_Request $request) {
        // WooCommerce satış toplamını ve bekleyen siparişleri çekelim
        if (!class_exists('WooCommerce')) {
            return new \WP_Error('wc_not_installed', 'WooCommerce kurulu değil.', ['status' => 500]);
        }

        $today = date('Y-m-d');
        $orders = wc_get_orders([
            'date_created' => '>=' . $today,
            'status' => ['completed', 'processing'],
            'limit' => -1
        ]);

        $total_sales = 0;
        foreach ($orders as $order) {
            $total_sales += $order->get_total();
        }

        $pending_orders_count = wc_orders_count('processing');

        // Form manager ile yeni istek/randevu sayısı
        $appointments = LibaWeb_Form_Manager::get_entries();
        $pending_appointments = count($appointments);

        return rest_ensure_response([
            'today_sales' => number_format($total_sales, 2, ',', '.') . ' ' . get_woocommerce_currency_symbol(),
            'pending_orders' => $pending_orders_count,
            'new_requests' => $pending_appointments
        ]);
    }

    public function get_orders(\WP_REST_Request $request) {
        if (!class_exists('WooCommerce')) return [];
        
        $orders = wc_get_orders([
            'limit' => 20,
            'orderby' => 'date',
            'order' => 'DESC',
        ]);

        $data = [];
        foreach ($orders as $order) {
            $data[] = [
                'id' => $order->get_id(),
                'status' => $order->get_status(),
                'total' => $order->get_formatted_order_total(),
                'customer_name' => $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(),
                'date' => $order->get_date_created()->date('Y-m-d H:i')
            ];
        }
        return rest_ensure_response($data);
    }

    public function get_products(\WP_REST_Request $request) {
        if (!class_exists('WooCommerce')) return [];

        $products = wc_get_products([
            'limit' => 20,
            'orderby' => 'date',
            'order' => 'DESC',
        ]);

        $data = [];
        foreach ($products as $product) {
            $data[] = [
                'id' => $product->get_id(),
                'name' => $product->get_name(),
                'price' => $product->get_price(),
                'stock_quantity' => $product->get_stock_quantity(),
                'image_url' => wp_get_attachment_url($product->get_image_id())
            ];
        }
        return rest_ensure_response($data);
    }

    public function get_appointments(\WP_REST_Request $request) {
        // class-form-manager yardımıyla uyumlu formlardan verileri çeker
        $entries = LibaWeb_Form_Manager::get_entries();
        return rest_ensure_response($entries);
    }
}
