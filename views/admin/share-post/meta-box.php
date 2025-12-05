<?php
/**
 * Variables that should be passed to the view
 * @var string $actionableUrl
 * @var string $label
 * @var bool $published
 * @var string $unpublishedText
 */
?>

<div class="metricool-share-meta-box">
    <?php if ($published): ?>
        <a href="<?php echo esc_url($actionableUrl) ?>" class="button button-primary button-large" target="_blank" style="width: 100%; text-align: center;">
            <span class="dashicons dashicons-megaphone" style="vertical-align: middle; margin-right: 5px;"></span>
            <?php echo esc_html($label); ?>
        </a>
    <?php else: ?>
        <i><?php echo esc_html($unpublishedText); ?></i>
    <?php endif; ?>
</div>
