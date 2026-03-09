<?php
/**
 * Plugin Name: MorPOS for WooCommerce
 * Description: MorPOS is a secure and easy-to-use payment gateway for WooCommerce, enabling businesses to accept credit and debit card payments online with ease.
 * Author: Morpara
 * Author URI: https://github.com/morpara
 * Version: 1.0.2
 * Requires Plugins: woocommerce
 * Requires at least: 6.0
 * Tested up to: 6.9
 * Requires PHP: 7.4
 * PHP tested up to: 8.4
 * WC requires at least: 8.0
 * WC tested up to: 10.1
 * Text Domain: morpos-for-woocommerce
 * Domain Path: /languages
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 */

if (!defined('ABSPATH')) {
    exit;
}

define('MORPOS_GATEWAY_VERSION', '1.0.2');
define('MORPOS_GATEWAY_PATH', plugin_dir_path(__FILE__));
define('MORPOS_GATEWAY_URL', plugin_dir_url(__FILE__));
define('MORPOS_CONVERSATION_KEY', AUTH_SALT . '|' . SECURE_AUTH_SALT . '|morpos:v1');

/**
 * MorPOS gateway plugin class.
 *
 * @class MorPOS_Loader
 */
class MorPOS_Loader
{
    /** minimum PHP version required by this plugin */
    public const MINIMUM_PHP_VERSION = '7.4.0';

    /** minimum WordPress version required by this plugin */
    public const MINIMUM_WP_VERSION = '6.0';

    /** minimum WooCommerce version required by this plugin */
    public const MINIMUM_WC_VERSION = '9.0';

    /** minimum TLS version required by this plugin */
    public const MINIMUM_TLS_VERSION = '1.2';

    /** recommended PHP version for this plugin */
    public const RECOMMENDED_PHP_VERSION = '8.2.0';

    /** recommended WordPress version for this plugin */
    public const RECOMMENDED_WP_VERSION = '6.8';

    /** recommended WooCommerce version for this plugin */
    public const RECOMMENDED_WC_VERSION = '10.0';

    /** recommended TLS version */
    public const RECOMMENDED_TLS_VERSION = '1.3';

    /** the plugin name, for displaying notices */
    public const PLUGIN_NAME = 'MorPOS for WooCommerce';

    /** @var MorPOS_Loader single instance of this class */
    private static $instance;

    /**
     * Plugin bootstrapping.
     */
    public function __construct()
    {
        // Declare compatibility with WooCommerce features.
        add_action('before_woocommerce_init', [$this, 'declare_features_compatibility']);

        // MorPOS Payments gateway class.
        add_action('plugins_loaded', [$this, 'init_plugin'], 0);

        // Make the MorPOS Payments gateway available to WC.
        add_filter('woocommerce_payment_gateways', [$this, 'add_gateway']);

        // Registers WooCommerce Blocks integration.
        add_action('woocommerce_blocks_loaded', [$this, 'woocommerce_gateway_block_support']);

        // AJAX handler for testing connection
        add_action('wp_ajax_morpos_test_connection', [MorPOS_Ajax::class, 'test_connection']);

        // Register frontend scripts and styles early
        add_action('init', [$this, 'register_frontend_assets']);
    }

    /**
     * Register frontend assets (scripts and styles).
     * They will be enqueued when needed.
     */
    public function register_frontend_assets()
    {
        // Register receipt page styles
        wp_register_style(
            'morpos-receipt',
            MORPOS_GATEWAY_URL . 'assets/css/morpos-receipt.css',
            [],
            MORPOS_GATEWAY_VERSION
        );

        // Register receipt page script
        wp_register_script(
            'morpos-receipt',
            MORPOS_GATEWAY_URL . 'assets/js/morpos-receipt.js',
            [],
            MORPOS_GATEWAY_VERSION,
            true
        );
    }

    /**
     * Initialize the plugin.
     */
    public function init_plugin()
    {
        // Plugin includes.
        self::includes();

        // If WooCommerce is not active, show admin notice.
        if (!class_exists('WC_Payment_Gateway')) {
            add_action('admin_notices', array(__CLASS__, 'morpos_wc_missing_notice'));
        }
    }

    /**
     * Admin notice for missing WooCommerce.
     */
    public function morpos_wc_missing_notice()
    {
        echo '<div class="notice notice-error"><p><strong>MorPOS:</strong> ' . esc_html__('This plugin requires WooCommerce. Please install and activate WooCommerce.', 'morpos-for-woocommerce') . '</p></div>';
    }

    /**
     * Add the MorPOS Payment gateway to the list of available gateways.
     *
     * @param array
     */
    public function add_gateway($gateways)
    {
        $options = get_option('woocommerce_morpos_settings', array());

        if (isset($options['hide_for_non_admin_users'])) {
            $hide_for_non_admin_users = $options['hide_for_non_admin_users'];
        } else {
            $hide_for_non_admin_users = 'no';
        }

        if (('yes' === $hide_for_non_admin_users && current_user_can('manage_options')) || 'no' === $hide_for_non_admin_users) {
            $gateways[] = 'MorPOS_Gateway';
        }

        return $gateways;
    }

    /**
     * Plugin includes.
     */
    public function includes()
    {
        require_once MORPOS_GATEWAY_PATH . 'includes/helpers.php';
        require_once MORPOS_GATEWAY_PATH . 'includes/class-morpos-logger.php';
        require_once MORPOS_GATEWAY_PATH . 'includes/class-morpos-api-client.php';
        require_once MORPOS_GATEWAY_PATH . 'includes/class-morpos-ajax.php';
        require_once MORPOS_GATEWAY_PATH . 'includes/class-morpos-gateway.php';
    }

    /**
     * Plugin url.
     *
     * @return string
     */
    public function plugin_url()
    {
        return untrailingslashit(plugins_url('/', __FILE__));
    }

    /**
     * Plugin url.
     *
     * @return string
     */
    public function plugin_abspath()
    {
        return trailingslashit(plugin_dir_path(__FILE__));
    }

    /**
     * Declare compatibility with WooCommerce features.
     */
    public function declare_features_compatibility()
    {
        if (class_exists('\Automattic\WooCommerce\Utilities\FeaturesUtil')) {
            // Declare compatibility with Custom Order Tables.
            \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('custom_order_tables', __FILE__, true);
            // Declare compatibility with Cart & Checkout Blocks.
            \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('cart_checkout_blocks', __FILE__, true);
        }
    }

    /**
     * Registers WooCommerce Blocks integration.
     */
    public function woocommerce_gateway_block_support()
    {
        if (class_exists('Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType')) {
            require_once MORPOS_GATEWAY_PATH . 'includes/class-morpos-payment-method.php';
            add_action(
                'woocommerce_blocks_payment_method_type_registration',
                function (Automattic\WooCommerce\Blocks\Payments\PaymentMethodRegistry $payment_method_registry) {
                    $payment_method_registry->register(new MorPOS_Payment_Method());
                }
            );
        }
    }

    /**
     * Gets the main plugin loader instance.
     * Ensures only one instance can be loaded.
     *
     * @return \MorPOS_Loader
     */
    public static function instance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }
}

// Initialize the plugin.
MorPOS_Loader::instance();
