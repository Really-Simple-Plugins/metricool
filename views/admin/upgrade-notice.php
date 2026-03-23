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
    .toplevel_page_metricool .rsp-metricool-upgrade-notice {
        margin: 16px;
    }
    .rsp-metricool-upgrade-notice {
        border-left:4px solid #333
    }
    .rsp-metricool-upgrade-notice .rsp-metricool-container {
        display: flex;
        padding:12px;
    }
    .rsp-metricool-upgrade-notice .rsp-metricool-container .dashicons {
        margin-right:5px;
        margin-left:15px;
    }
    .rsp-metricool-upgrade-notice .rsp-metricool-upgrade-notice-image {
        width: 80px;
        height: 80px;
    }
    .rsp-metricool-upgrade-notice .rsp-metricool-upgrade-notice-image img{
        width: 100%;
        height: 100%;
        object-fit: contain;
        object-position: center;
    }
    .rsp-metricool-upgrade-notice .rsp-metricool-buttons-row {
        margin-top:10px;
        display: flex;
        align-items: center;
    }
    .rsp-metricool-upgrade-notice .rsp-metricool-upgrade-notice-form {
        margin-left: 30px;
    }
    .rsp-metricool-upgrade-notice .rsp-metricool-upgrade-notice-form button.link {
        background: none;
        border: none;
        color: #2271b1;
        text-decoration: underline;
        cursor: pointer;
        padding: 0;
        font-size: inherit;
    }
    <?php if (is_rtl()) : ?>
    .rsp-metricool-upgrade-notice .rsp-metricool-container .dashicons {
        margin-left:5px;
        margin-right:15px;
    }
    .rsp-metricool-upgrade-notice {
        border-left: 0;
        border-right: 4px solid #333;
    }
    <?php endif; ?>
</style>

<div id="message" class="updated fade notice rsp-metricool-upgrade-notice really-simple-plugins">
    <div class="rsp-metricool-container">
        <div class="rsp-metricool-upgrade-notice-image"><img src="<?php echo esc_url($logoUrl); ?>" alt="metricool-logo"></div>
        <form class="rsp-metricool-upgrade-notice-form" action="" method="POST">
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
