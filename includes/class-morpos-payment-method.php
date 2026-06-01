<?php

if (!defined('ABSPATH')) {
    exit;
}

use Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType;

class MorPOS_Payment_Method extends AbstractPaymentMethodType
{
    protected $name = 'morpos';

    /**
     * Settings from the WP options table
     *
     * @var array
     */
    protected $settings = [];
    
    /**
     * Initialize the payment method.
     */
    public function initialize()
    {
        $this->settings = get_option('woocommerce_morpos_settings', []);
    }

    /**
     * Returns an array of script handles to enqueue for this payment method in
     * the frontend context
     *
     * @return string[]
     */
    public function get_payment_method_script_handles()
    {
        // Register frontend script
        wp_register_script(
            $this->name,
            MORPOS_GATEWAY_URL . 'assets/js/morpos.js',
            ['wc-blocks-registry', 'wc-settings', 'wp-element', 'wp-i18n'],
            MORPOS_GATEWAY_VERSION,
            true
        );
        // Register script translations
        wp_set_script_translations(
            $this->name,
            'morpos-for-woocommerce',
            MORPOS_GATEWAY_PATH . 'languages'
        );

        // Register frontend styles
        wp_register_style(
            $this->name,
            MORPOS_GATEWAY_URL . 'assets/css/morpos.css',
            ['wp-components'],
            MORPOS_GATEWAY_VERSION
        );
        wp_enqueue_style($this->name);

        return [$this->name];
    }

    /**
     * Checks if the payment method is active.
     *
     * @return bool True if active, false otherwise.
     */
    public function is_active()
    {
        if (!isset($this->settings['enabled']) || 'yes' !== $this->settings['enabled']) {
            return false;
        }

        // Hide when required credentials are missing — payment would be guaranteed to fail
        foreach (['merchant_id', 'client_id', 'client_secret', 'api_key'] as $key) {
            if (empty($this->settings[$key])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Returns payment method data to be used in the frontend.
     *
     * @return array Associative array containing payment method data.
     */
    public function get_payment_method_data()
    {
        return [
            'testmode' => isset($this->settings['testmode']) && 'yes' === $this->settings['testmode'],
            'logoUrl' => MORPOS_GATEWAY_URL . 'assets/img/morpos-logo-small.png',
            'cardsStripUrl' => MORPOS_GATEWAY_URL . 'assets/img/card-logos.png',
        ];
    }
}
