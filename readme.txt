=== MorPOS for WooCommerce ===
Contributors: morpara
Tags: payment, woocommerce, credit card, payment gateway, morpos
Requires at least: 6.0
Tested up to: 6.9
Stable tag: 1.0.2
Requires PHP: 7.4
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

MorPOS is a secure and easy-to-use payment gateway for WooCommerce, enabling businesses to accept credit and debit card payments online with ease.

== Description ==

**MorPOS for WooCommerce** is a secure and easy-to-use payment gateway plugin that integrates the Morpara MorPOS payment system with WooCommerce stores.

= Features =

* **WooCommerce Integration** – Seamlessly adds MorPOS as a payment method
* **Secure Payments** – Hosted Payment Page (HPP) for maximum security
* **WooCommerce Blocks Support** – Compatible with new Cart and Checkout blocks
* **Multi-Currency** – Supports TRY, USD, EUR currencies
* **Multiple Payment Options** – Credit cards, debit cards, and installment payments
* **Sandbox Mode** – Test environment for development
* **Easy Configuration** – Simple admin panel setup
* **Security Features** – TLS 1.2+ requirement, signed API communication

= Requirements =

* WordPress 6.0 or higher
* WooCommerce 9.0 or higher
* PHP 7.4 or higher (8.2+ recommended)
* TLS 1.2 or higher
* PHP extensions: cURL, json, hash, openssl

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/morpos-for-woocommerce/` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Navigate to WooCommerce → Settings → Payments → MorPOS to configure the plugin.

= Configuration =

1. Enter your Merchant ID, Client ID, Client Secret, and API Key (provided by Morpara).
2. Choose your preferred payment method: Hosted Payment Page (recommended) or Embedded Payment.
3. Enable or disable Test Mode for sandbox testing.
4. Click "Test Connection" to verify your credentials.

== Frequently Asked Questions ==

= What currencies are supported? =

MorPOS supports TRY (Turkish Lira), USD (US Dollar), and EUR (Euro).

= How do I test the plugin? =

Enable "Test Mode" in the plugin settings. This uses sandbox endpoints so no real transactions are processed.

= What are the server requirements? =

WordPress 6.0+, WooCommerce 9.0+, PHP 7.4+ (8.2+ recommended), and TLS 1.2+. The plugin includes a built-in system requirements checker on the settings page.

= What should I do if the connection test fails? =

Verify all API credentials are correct, ensure your server supports TLS 1.2+, check that outbound HTTPS connections are allowed, and ensure the server time is accurate.

== External services ==

This plugin connects to the Morpara MorPOS payment gateway API to process credit and debit card payments on your WooCommerce store.

= What the service is =

MorPOS is a payment gateway service provided by Morpara that enables e-commerce websites to securely accept credit and debit card payments, including installment payments. The plugin communicates with the MorPOS API to initiate, process, and verify payment transactions.

= What data is sent and when =

When a customer initiates a payment during checkout, the plugin sends the following data to the MorPOS API:

* Order amount and currency
* Merchant credentials (Merchant ID, Client ID, Client Secret, API Key)
* A unique conversation identifier for transaction tracking

The customer's payment card details are entered directly on the MorPOS hosted payment page or embedded payment form and are handled entirely by MorPOS — card data is never stored or transmitted by this plugin.

The plugin communicates with the MorPOS API in the following scenarios:

* **During checkout**: To create a payment session and redirect the customer to the payment page, or to generate an embedded payment form.
* **After payment**: To verify the payment status via a server-side check.
* **On the admin settings page**: To test the API connection when the merchant clicks "Test Connection" or saves settings.

= Service endpoints =

* Production: `https://sale-gateway.morpara.com`
* Sandbox (test mode): `https://finagopay-pf-sale-api-gateway.prp.morpara.com`

= Service links =

* [Morpara Terms and Conditions](https://www.morpara.com/en/privacy-and-security/terms-and-conditions/)
* [Morpara Privacy Policy](https://www.morpara.com/en/privacy-and-security/privacy-policy/)

== Screenshots ==

1. MorPOS payment gateway settings page
2. System requirements checker
3. Checkout payment option

== Changelog ==

= 1.0.2 =
* Security: Added nonce verification for receipt notice redirects
* Security: Switched to allowlist-based parameter collection for payment return data
* Security: Added input sanitization for admin settings page query parameters
* Improvement: Moved escaping to template layer for requirements table
* Updated translations

= 1.0.1 =
* Refactored class naming conventions
* Changed text domain to `morpos-for-woocommerce` for WordPress.org compliance
* Added receipt page styles and scripts
* Improved callback page handling
* Added frontend asset registration
* Updated translations and language files
* Updated documentation

= 1.0.0 =
* Initial release

== Upgrade Notice ==

= 1.0.2 =
Security improvements: nonce verification, allowlist-based parameter handling, and input sanitization.

= 1.0.1 =
Code refactoring, text domain update for WordPress.org compliance, and improved receipt page.

= 1.0.0 =
Initial release of MorPOS for WooCommerce.
