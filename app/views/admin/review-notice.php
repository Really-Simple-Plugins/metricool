<?php
/**
 * Variables that should be passed to the view
 * @var string $logoUrl
 * @var string $reviewUrl
 * @var string $reviewMessage
 * @var string $reviewAction
 * @var string $reviewNonceName
 */
?>

<style>
    .toplevel_page_metricool .rsp-metricool-review {
        margin: 16px;
    }
    .rsp-metricool-review {
        border-left:4px solid #333
    }
    .rsp-metricool-review .rsp-metricool-container {
        display: flex;
        padding:12px;
    }
    .rsp-metricool-review .rsp-metricool-container .dashicons {
        margin-right:5px;
        margin-left:15px;
    }
    .rsp-metricool-review .rsp-metricool-review-image {
        width: 80px;
        height: 80px;
    }
    .rsp-metricool-review .rsp-metricool-review-image img{
        width: 100%;
        height: 100%;
        object-fit: contain;
        object-position: center;
    }
    .rsp-metricool-review .rsp-metricool-buttons-row {
        margin-top:10px;
        display: flex;
        align-items: center;
    }
    .rsp-metricool-review .rsp-metricool-review-form {
        margin-left: 30px;
    }
    .rsp-metricool-review .rsp-metricool-review-form button.link {
        background: none;
        border: none;
        color: #2271b1;
        text-decoration: underline;
        cursor: pointer;
        padding: 0;
        font-size: inherit;
    }
    <?php if (is_rtl()): ?>
         .rsp-metricool-review .rsp-metricool-container .dashicons {
             margin-left:5px;
             margin-right:15px;
         }
        .rsp-metricool-review {
            border-left: 0;
            border-right: 4px solid #333;
        }
    <?php endif; ?>
</style>

<div id="message" class="updated fade notice rsp-metricool-review really-simple-plugins">
    <div class="rsp-metricool-container">
        <div class="rsp-metricool-review-image"><img src="<?php echo esc_url($logoUrl); ?>" alt="review-logo"></div>
        <form class="rsp-metricool-review-form" action="" method="POST">
            <?php wp_nonce_field($reviewAction, $reviewNonceName); ?>
            <input type="hidden" name="rsp_metricool_review_form" value="1">
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
        </form>
    </div>
</div>