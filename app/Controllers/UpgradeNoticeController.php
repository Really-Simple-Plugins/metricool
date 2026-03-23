<?php

declare(strict_types=1);

namespace Metricool\Controllers;

use Metricool\Traits\HasViews;
use Metricool\Traits\HasAllowlistControl;
use Metricool\Interfaces\ControllerInterface;
use Metricool\Support\Helpers\Storages\RequestStorage;
use Metricool\Support\Helpers\Storages\EnvironmentConfig;

class UpgradeNoticeController implements ControllerInterface
{
    use HasViews;
    use HasAllowlistControl;

    private EnvironmentConfig $env;
    private RequestStorage $request;
    private string $dismissAction = 'rsp_metricool_upgrade_notice_dismiss';
    private string $dismissNonceName = 'rsp_metricool_upgrade_notice_nonce';

    public function __construct(EnvironmentConfig $env, RequestStorage $request)
    {
        $this->env = $env;
        $this->request = $request;
    }

    public function register(): void
    {
        add_action('metricool_plugin_version_upgrade', [$this, 'maybeSetUpgradeNoticeFlag'], 10, 2);

        if ($this->adminAccessAllowed() === false) {
            return;
        }

        add_action('admin_notices', [$this, 'showUpgradeNotice']);
        add_action('admin_init', [$this, 'processDismissFormSubmit']);
    }

    /**
     * Set a flag to show the upgrade notice when upgrading from a legacy
     * (pre-2.0) version of the plugin.
     */
    public function maybeSetUpgradeNoticeFlag(string $previousVersion, string $newVersion): void
    {
        if (version_compare($previousVersion, '2.0.0', '<') === false) {
            return;
        }

        update_option('_metricool_show_upgrade_notice', true, false);
    }

    /**
     * Show the upgrade notice in the WordPress admin area.
     */
    public function showUpgradeNotice(): void
    {
        if ($this->shouldShowUpgradeNotice() === false) {
            return;
        }

        $this->render('admin/upgrade-notice', [
            'dashboardUrl' => $this->env->getUrl('plugin.dashboard_url'),
            'dismissAction' => $this->dismissAction,
            'dismissNonceName' => $this->dismissNonceName,
        ]);
    }

    /**
     * Process the dismiss form submission.
     */
    public function processDismissFormSubmit(): void
    {
        if ($this->request->isEmpty('global.rsp_metricool_upgrade_notice_dismiss_form')) {
            return;
        }

        $nonce = $this->request->get('global.' . $this->dismissNonceName);
        if (wp_verify_nonce($nonce, $this->dismissAction) === false) {
            return;
        }

        delete_option('_metricool_show_upgrade_notice');
    }

    /**
     * Determine if the upgrade notice should be shown.
     */
    private function shouldShowUpgradeNotice(): bool
    {
        return (bool) get_option('_metricool_show_upgrade_notice', false);
    }
}
