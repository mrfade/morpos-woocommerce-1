<?php
if (!defined('ABSPATH')) {
  exit; // Exit if accessed directly
}

// Print the styles
wp_print_styles('morpos-receipt');
?>
<?php if ($html): ?>
<section class="morpos-mini" role="status" aria-live="polite">
  <img
    class="morpos-logo"
    src="<?php echo esc_url($logo_url); ?>"
    alt="<?php echo esc_attr($provider_name); ?>"
  />

  <h2 class="morpos-title">
    <?php echo esc_html__('Almost there', 'morpos-for-woocommerce'); ?>
  </h2>
  <p class="morpos-lead">
    <?php echo esc_html__('We’ve received your order. Please complete the payment to finish your purchase.', 'morpos-for-woocommerce'); ?>
  </p>

  <p class="morpos-meta">
    <span class="morpos-order">
      <?php echo esc_html__('Order number:', 'morpos-for-woocommerce'); ?>
      #<?php echo esc_html($order->get_order_number()); ?>
    </span>
  </p>

  <div class="morpos-iframe-wrap">
    <iframe
      id="morpos-frame"
      class="morpos-iframe"
      title="<?php echo esc_attr($provider_name); ?>"
      loading="eager"
      allow="payment *; clipboard-write; fullscreen"
      referrerpolicy="no-referrer-when-downgrade"
    ></iframe>
  </div>

  <p class="morpos-note">
    <?php echo wp_kses_post(__('<strong>Do not close this window</strong> until payment succeeds and you’re redirected.', 'morpos-for-woocommerce')); ?>
  </p>
</section>
<?php
// Add inline data and print the script
wp_add_inline_script(
    'morpos-receipt',
    'var morposReceiptData = ' . wp_json_encode($receipt_data) . ';',
    'before'
);
wp_print_scripts('morpos-receipt');
endif;
?>
