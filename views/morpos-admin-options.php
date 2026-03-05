<?php
if (!defined('ABSPATH')) {
  exit; // Exit if accessed directly
}
?>
<div class="morpos-settings">
  <div class="morpos-header">
    <div class="header-left">
      <h2 class="morpos-h2"><?php echo esc_html($this->get_method_title()); ?></h2>
      <p class="morpos-desc"><?php echo esc_html($this->get_method_description()); ?></p>
      <p class="morpos-version"><?php echo esc_html__('Version:', 'morpos-for-woocommerce') . ' ' . esc_html(MORPOS_GATEWAY_VERSION); ?>
      </p>
    </div>
    <div class="header-right">
      <img class="morpos-logo" src="<?php echo esc_url(MORPOS_GATEWAY_URL . 'assets/img/morpos-logo.png'); ?>"
        alt="MorPOS Logo" />
    </div>
  </div>

  <div class="morpos-connection">
    <span class="pill pill-setup"><?php echo esc_html__('Setup', 'morpos-for-woocommerce'); ?></span>
    <span class="pill pill-ok"><?php echo esc_html__('Connection Successful', 'morpos-for-woocommerce'); ?></span>
    <span class="pill pill-fail"><?php echo esc_html__('Connection Failed', 'morpos-for-woocommerce'); ?></span>
    <button type="button"
      class="button button-primary morpos-test-btn"><?php echo esc_html__('Test Connection', 'morpos-for-woocommerce'); ?></button>
  </div>

  <table class="form-table">
    <?php $this->generate_settings_html(); ?>
  </table>
  <div id="morpos-submit-anchor"></div>

  <div class="morpos-requirements">
    <h2><?php echo esc_html__('System Requirements', 'morpos-for-woocommerce'); ?></h2>
    <p>
      <?php echo esc_html__('The following table shows the current status of your server environment. Please ensure all requirements are met for optimal performance.', 'morpos-for-woocommerce'); ?>
    </p>
    <?php $this->render_requirements_table(); ?>
  </div>
</div>