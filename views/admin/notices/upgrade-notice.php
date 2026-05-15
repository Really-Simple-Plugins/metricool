<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Inner content for the legacy upgrade admin notice.
 * Rendered inside the layout.php template.
 * CTA and dismiss buttons are rendered by the layout.
 */

?>

<p><strong><?php esc_html_e('You have just upgraded to the new Metricool plugin', 'metricool'); ?></strong></p>
<p>
    <?php echo wp_kses_post(sprintf(
        __('Please <strong>sign in</strong> to discover all new functionality', 'metricool')
    )); ?>
</p>
