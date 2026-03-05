<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
?>
<div class="morpos-reqs">
  <div class="morpos-reqs__head"><?php echo esc_html__('Requirements', 'morpos-for-woocommerce'); ?></div>
  <table>
    <thead>
      <tr>
        <th><?php echo esc_html__('Component', 'morpos-for-woocommerce'); ?></th>
        <th class="col-current"><?php echo esc_html__('Current', 'morpos-for-woocommerce'); ?></th>
        <th><?php echo esc_html__('Recommended', 'morpos-for-woocommerce'); ?></th>
        <th><?php echo esc_html__('Required (minimum)', 'morpos-for-woocommerce'); ?></th>
        <th class="col-status"><?php echo esc_html__('Status', 'morpos-for-woocommerce'); ?></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($rows as $r): ?>
        <tr>
          <td><?php echo esc_html($r['label']); ?></td>
          <td><?php echo esc_html($r['cur']); ?></td>
          <td><?php echo esc_html($r['rec']); ?></td>
          <td><?php echo esc_html($r['req']); ?></td>
          <td>
            <span class="morpos-badge <?php echo esc_attr($r['status']['class']); ?>">
              <?php
              if ($r['status']['class'] === 'morpos-ok') {
                echo '&#x2714;';
              }
              if ($r['status']['class'] === 'morpos-warning') {
                echo '&#9888;&#xfe0f;';
              }
              if ($r['status']['class'] === 'morpos-danger') {
                echo '&#10060;';
              }
              ?>
              <?php echo esc_html($r['status']['hint']); ?>
            </span>

            <?php if ($r['status']['class'] === 'morpos-warning' && $r['label'] === 'PHP'): ?>
              <span
                class="morpos-hint"><?php echo esc_html__('Usable, but please upgrade to improve performance and security.', 'morpos-for-woocommerce'); ?></span>
            <?php endif; ?>

            <?php if ($r['label'] === 'TLS' && $r['status']['class'] === 'morpos-danger'): ?>
              <span class="morpos-hint">
                <?php echo esc_html__('Payments cannot be processed until the server supports TLS 1.2 or newer.', 'morpos-for-woocommerce'); ?>
              </span>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>