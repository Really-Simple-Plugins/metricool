<?php
/**
 * Variables that should be passed to the view
 * @var string $logoUrl
 * @var string $dashboardUrl
 * @var string $dismissAction
 * @var string $dismissNonceName
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
            <input type="hidden" name="rsp_metricool_upgrade_notice_dismiss_form" value="1">
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
        </form>
    </div>
</div>
