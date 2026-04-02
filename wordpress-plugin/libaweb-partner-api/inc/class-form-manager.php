<?php
if (!defined('ABSPATH')) {
    exit;
}

class LibaWeb_Form_Manager {

    /**
     * Sitedeki aktif form eklentilerini tarar ve tüm girdileri tek tip (standart) formata dönüştürür
     */
    public static function get_entries($limit = 20) {
        $entries = [];

        // 1. Flamingo (Contact Form 7 eklentisi veritabanına kaydeder)
        if (class_exists('Flamingo_Inbound_Message')) {
            $flamingo_args = [
                'post_type' => 'flamingo_inbound',
                'posts_per_page' => $limit,
                'post_status' => 'publish'
            ];
            $posts = get_posts($flamingo_args);
            foreach ($posts as $post) {
                // CF7 genelde 'your-name', 'your-email', 'your-subject', 'your-message' kullanır
                $meta = get_post_meta($post->ID, '_field_your-name', true);
                if (!$meta) $meta = 'İsimsiz Gönderici';
                
                $entries[] = [
                    'id' => $post->ID,
                    'source' => 'Contact Form 7',
                    'sender' => $meta,
                    'subject' => get_post_meta($post->ID, '_field_your-subject', true) ?: 'İletişim Talebi',
                    'date' => get_the_date('Y-m-d H:i', $post),
                    'status' => 'bekliyor'
                ];
            }
        }

        // 2. WPForms lite veya pro versiyonu kullanılıyorsa
        global $wpdb;
        $wpforms_table = $wpdb->prefix . 'wpforms_entries';
        if ($wpdb->get_var("SHOW TABLES LIKE '$wpforms_table'") === $wpforms_table) {
            $results = $wpdb->get_results("SELECT * FROM $wpforms_table ORDER BY date DESC LIMIT $limit");
            foreach ($results as $row) {
                // Fields genelde JSON barındırır
                $fields = json_decode($row->fields, true);
                $name = 'Belirtilmedi';
                
                // İlk text veya name alanını bul (id'sine bakarak basit bir tarama)
                if (is_array($fields)) {
                    foreach ($fields as $field) {
                        if (in_array($field['type'], ['name', 'text']) && !empty($field['value'])) {
                            $name = $field['value'];
                            break;
                        }
                    }
                }

                $entries[] = [
                    'id' => $row->entry_id,
                    'source' => 'WPForms',
                    'sender' => $name,
                    'subject' => 'Yeni Form Kaydı',
                    'date' => date('Y-m-d H:i', strtotime($row->date)),
                    'status' => $row->status
                ];
            }
        }

        // 3. Fallback: Veri yoksa Flutter çökmeyecek şekilde boş dizi veya mock
        if (empty($entries)) {
            // Test için sahte veri de döndürülebilir, şimdilik boş dizi
        }

        // Tarihe göre sırala (En yeni en üstte)
        usort($entries, function($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });

        return array_slice($entries, 0, $limit);
    }
}
