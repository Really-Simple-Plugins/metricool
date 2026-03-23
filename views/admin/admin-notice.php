<?php
/**
 * Base template for admin notices. Provides shared layout, styles,
 * logo, and form wrapper. Each notice passes its own inner content
 * via the $content variable (rendered by the calling controller).
 *
 * Variables that should be passed to the view
 * @var string $logoUrl
 * @var string $dismissAction
 * @var string $dismissNonceName
 * @var string $formName The hidden input name used to identify the form submission.
 * @var string $content The inner HTML content specific to each notice type.
 */
?>

<style>
    .toplevel_page_metricool .rsp-metricool-admin-notice {
        margin: 16px;
    }
    .rsp-metricool-admin-notice {
        border-left:4px solid #333
    }
    .rsp-metricool-admin-notice .rsp-metricool-container {
        display: flex;
        padding:12px;
    }
    .rsp-metricool-admin-notice .rsp-metricool-container .dashicons {
        margin-right:5px;
        margin-left:15px;
    }
    .rsp-metricool-admin-notice .rsp-metricool-admin-notice-image {
        width: 80px;
        height: 80px;
    }
    .rsp-metricool-admin-notice .rsp-metricool-admin-notice-image img{
        width: 100%;
        height: 100%;
        object-fit: contain;
        object-position: center;
    }
    .rsp-metricool-admin-notice .rsp-metricool-buttons-row {
        margin-top:10px;
        display: flex;
        align-items: center;
    }
    .rsp-metricool-admin-notice .rsp-metricool-admin-notice-form {
        margin-left: 30px;
    }
    .rsp-metricool-admin-notice .rsp-metricool-admin-notice-form button.link {
        background: none;
        border: none;
        color: #2271b1;
        text-decoration: underline;
        cursor: pointer;
        padding: 0;
        font-size: inherit;
    }

    .rsp-metricool-admin-notice.rtl {
        border-left: 0;
        border-right: 4px solid #333;
    }
    .rsp-metricool-admin-notice.rtl .rsp-metricool-container .dashicons {
        margin-left:5px;
        margin-right:15px;
    }
</style>

<div id="message" class="updated fade notice rsp-metricool-admin-notice really-simple-plugins <?php echo is_rtl() ? 'rtl' : 'ltr'; ?>">
    <div class="rsp-metricool-container">
        <div class="rsp-metricool-admin-notice-image"><img src="<?php echo esc_url($logoUrl); ?>" alt="metricool-logo"></div>
        <form class="rsp-metricool-admin-notice-form" action="" method="POST">
            <?php wp_nonce_field($dismissAction, $dismissNonceName); ?>
            <input type="hidden" name="<?php echo esc_attr($formName); ?>" value="1">
            <?php
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $content is rendered by our own view templates
            echo $content;
            ?>
        </form>
    </div>
</div>
