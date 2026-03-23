<?php
/**
 * Variables that should be passed to the view
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
        border-left: 4px solid #333;
    }
    .rsp-metricool-upgrade-notice .rsp-metricool-container {
        display: flex;
        padding: 12px;
    }
    .rsp-metricool-upgrade-notice .rsp-metricool-upgrade-content {
        margin-left: 15px;
    }
    .rsp-metricool-upgrade-notice .rsp-metricool-upgrade-content p {
        margin: 4px 0;
    }
    .rsp-metricool-upgrade-notice .rsp-metricool-buttons-row {
        margin-top: 10px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .rsp-metricool-upgrade-notice .rsp-metricool-upgrade-form button.link {
        background: none;
        border: none;
        color: #2271b1;
        text-decoration: underline;
        cursor: pointer;
        padding: 0;
        font-size: inherit;
    }
    <?php if (is_rtl()): ?>
        .rsp-metricool-upgrade-notice {
            border-left: 0;
            border-right: 4px solid #333;
        }
        .rsp-metricool-upgrade-notice .rsp-metricool-upgrade-content {
            margin-left: 0;
            margin-right: 15px;
        }
    <?php endif; ?>
</style>

<div id="message" class="updated fade notice rsp-metricool-upgrade-notice really-simple-plugins">
    <div class="rsp-metricool-container">
        <form class="rsp-metricool-upgrade-form" action="" method="POST">
            <?php wp_nonce_field($dismissAction, $dismissNonceName); ?>
            <input type="hidden" name="rsp_metricool_upgrade_notice_dismiss_form" value="1">
            <div class="rsp-metricool-upgrade-content">
                <p><strong><?php esc_html_e('Welcome to the new Metricool!', 'metricool'); ?></strong></p>
                <p>
                    <?php esc_html_e('The Metricool plugin has been completely redesigned with a fresh new interface and improved features. Head over to the Metricool dashboard to explore the new experience.', 'metricool'); ?>
                </p>
                <div class="rsp-metricool-buttons-row">
                    <a class="button button-primary" href="<?php echo esc_url($dashboardUrl); ?>">
                        <?php esc_html_e('Go to Metricool', 'metricool'); ?>
                    </a>
                    <button type="submit" class="link" title="<?php echo esc_attr__('Dismiss this notice.', 'metricool'); ?>">
                        <?php esc_html_e('Dismiss', 'metricool'); ?>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
