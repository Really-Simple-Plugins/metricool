<?php
/**
 * Variables that should be passed to the view
 * @var string $actionableUrl
 */
?>

<a href="<?php echo esc_url($actionableUrl) ?>" class="button button-primary" target="_blank">
    <?php esc_html_e('Share this post', 'metricool') ?>
    <span class="dashicons dashicons-megaphone" style="vertical-align: middle"></span>
</a>
