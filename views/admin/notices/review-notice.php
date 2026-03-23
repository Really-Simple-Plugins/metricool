<?php
/**
 * Inner content for the review admin notice.
 * Rendered inside the base admin-notice.php template.
 *
 * Variables that should be passed to the view
 * @var string $reviewUrl
 * @var string $reviewMessage
 */
?>

<?php echo wp_kses_post(wpautop($reviewMessage)); ?>
<div class="rsp-metricool-buttons-row">
    <a class="button button-primary" target="_blank" rel="noopener noreferrer" href="<?php echo esc_url($reviewUrl); ?>">
        <?php esc_html_e('Leave a review', 'metricool'); ?>
    </a>
    <div class="dashicons dashicons-calendar"></div>
    <button type="submit" class="link" name="rsp_metricool_review_choice" value="later" title="<?php echo esc_attr__('Dismiss this notice for 30 days.', 'metricool'); ?>">
        <?php esc_html_e('Maybe later', 'metricool'); ?>
    </button>
    <div class="dashicons dashicons-no-alt"></div>
    <button type="submit" class="link" name="rsp_metricool_review_choice" value="never" title="<?php echo esc_attr__('Dismiss this notice forever.', 'metricool'); ?>">
        <?php esc_html_e('Don\'t show again', 'metricool'); ?>
    </button>
</div>
