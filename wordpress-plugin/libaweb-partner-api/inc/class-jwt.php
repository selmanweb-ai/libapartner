<?php
if (!defined('ABSPATH')) {
    exit;
}

class LibaWeb_JWT {

    private static function get_secret() {
        if (defined('LIBAWEB_JWT_SECRET')) {
            return LIBAWEB_JWT_SECRET;
        }
        return get_option('libaweb_jwt_secret', 'fallback_secret_must_not_be_used');
    }

    public static function generate_token($user_id) {
        $secret = self::get_secret();
        
        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
        $payload = json_encode([
            'iss' => get_bloginfo('url'),
            'iat' => time(),
            'exp' => time() + (DAY_IN_SECONDS * 30), // 30 gün geçerli
            'data' => [
                'user' => [
                    'id' => $user_id
                ]
            ]
        ]);

        $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
        $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));
        
        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $secret, true);
        $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
        
        return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
    }

    public static function validate_token($token) {
        $parts = explode('.', $token);
        if (count($parts) != 3) {
            return false;
        }

        $secret = self::get_secret();
        $header = $parts[0];
        $payload = $parts[1];
        $signature = $parts[2];

        $valid_signature = hash_hmac('sha256', $header . "." . $payload, $secret, true);
        $valid_base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($valid_signature));

        if ($signature === $valid_base64UrlSignature) {
            $payload_data = json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], $payload)), true);
            if ($payload_data['exp'] < time()) {
                return false; // Token süresi doldu
            }
            return $payload_data['data']['user']['id'];
        }

        return false;
    }
}
