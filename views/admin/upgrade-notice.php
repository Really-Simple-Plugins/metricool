<?php
/**
 * Inner content for the legacy upgrade admin notice.
 * Rendered inside the base admin-notice.php template.
 *
 * Variables that should be passed to the view
 * @var string $dashboardUrl
 */
?>

<p><strong><?php esc_html_e('You have just upgraded to the new Metricool plugin', 'metricool'); ?></strong></p>
<p>
    <?php esc_html_e('Please sign in to discover all new functionality', 'metricool'); ?>
</p>
<div class="rsp-metricool-buttons-row">
    <a class="button button-primary" href="<?php echo esc_url($dashboardUrl); ?>">
        <?php esc_html_e('Sign in now!', 'metricool'); ?>
    </a>
    <div class="dashicons dashicons-no-alt"></div>
    <button type="submit" class="link" title="<?php echo esc_attr__('Dismiss this notice.', 'metricool'); ?>">
        <?php esc_html_e('Don\'t show again', 'metricool'); ?>
    </button>
</div>
