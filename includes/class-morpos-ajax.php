<?php

if (!defined('ABSPATH')) {
    exit;
}

class MorPOS_Ajax
{
    /**
     * Handle AJAX request to test connection with MorPOS API.
     */
    public static function test_connection()
    {
        if (!current_user_can('manage_woocommerce')) {
            wp_send_json_error(['message' => __('Unauthorized', 'morpos-for-woocommerce')], 403);
        }

        check_ajax_referer('morpos_admin', 'nonce');

        $fields = ['merchant_id', 'client_id', 'client_secret', 'api_key'];
        $credentials = [];
        if (isset($_POST['credentials']) && is_array($_POST['credentials'])) {
            $posted = wp_unslash($_POST['credentials']);
            $credentials = array_reduce($fields, fn($carry, $field) => array_merge($carry, [$field => isset($posted[$field]) ? sanitize_text_field($posted[$field]) : null]), []);
        }

        $isValid = array_reduce($fields, fn($carry, $field) => $carry && !empty($credentials[$field]), true);
        if (!$isValid) {
            wp_send_json_error(['status' => 'fail', 'message' => __('Please fill in all required fields.', 'morpos-for-woocommerce')]);
        }

        $api = new MorPOS_API_Client(
            $credentials['client_id'],
            $credentials['client_secret'],
            $credentials['merchant_id'],
            '',
            $credentials['api_key'],
            sanitize_text_field($_POST['testmode'] ?? '') === 'yes' ? 'sandbox' : 'production',
        );

        $result = $api->make_test_connection();
        $ok = $result['ok'] === true && isset($result['data']) && isset($result['data']['responseCode']) && $result['data']['responseCode'] === 'B0000';

        // NOTE: Intentionally NOT persisted to the settings option here.
        // Rewriting the whole option from a separate AJAX request can clobber
        // freshly saved credentials when a stale (object-cached) value is read back.
        // The status is persisted on settings save (process_admin_options) instead.

        if ($ok) {
            MorPOS_Logger::info('TestConnection: successful');
            wp_send_json_success(['status' => 'ok', 'message' => __('Connection successful.', 'morpos-for-woocommerce')]);
        }

        $errMsg = $result['error'] ?? ('HTTP ' . ($result['http'] ?? 'unknown'));
        MorPOS_Logger::warning('TestConnection: failed', ['error' => $errMsg]);
        wp_send_json_error(['status' => 'fail', 'message' => __('Connection failed.', 'morpos-for-woocommerce') . ' ' . $errMsg]);
    }
}
