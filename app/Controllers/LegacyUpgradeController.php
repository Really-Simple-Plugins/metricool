<?php

declare(strict_types=1);

namespace Metricool\Controllers;

use Metricool\Traits\HasViews;
use Metricool\Traits\HasAllowlistControl;
use Metricool\Interfaces\ControllerInterface;
use Metricool\Support\Helpers\Storages\RequestStorage;
use Metricool\Support\Helpers\Storages\EnvironmentConfig;

class LegacyUpgradeController implements ControllerInterface
{
    use HasViews;
    use HasAllowlistControl;

    private EnvironmentConfig $env;
    private RequestStorage $request;

    private string $formAction = 'rsp_metricool_upgrade_notice_dismiss';
    private string $formNonce = 'rsp_metricool_upgrade_notice_nonce';

    public function __construct(EnvironmentConfig $env, RequestStorage $request)
    {
        $this->env = $env;
        $this->request = $request;
    }

    public function register(): void
    {
        add_action('metricool_plugin_version_upgrade', [$this, 'maybeSetLegacyFlags'], 10, 2);

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
    public function maybeSetLegacyFlags(string $previousVersion, string $newVersion): void
    {
        if (version_compare($previousVersion, '2.0.0', '<') === false) {
            return;
        }

        update_option('metricool_show_upgrade_notice', true, false);
        update_option('metricool_from_legacy_plugin', true, false);
    }

    /**
     * Delete the legacy flags. Should happen after the first successful authentication.
     */
    public function deleteLegacyFlags(): void
    {
        delete_option('metricool_from_legacy_plugin');
        delete_option('metricool_show_upgrade_notice');
    }

    /**
     * Show the upgrade notice in the WordPress admin area.
     */
    public function showUpgradeNotice(): void
    {
        if ($this->shouldShowUpgradeNotice() === false) {
            return;
        }

        $this->render('admin/notices/layout', [
            'logoUrl' => $this->env->getUrl('plugin.assets_url') . 'img/mc-logo.svg',
            'formAction' => $this->formAction,
            'formNonceName' => $this->formNonce,
            'formName' => 'rsp_metricool_upgrade_notice_dismiss_form',
            'content' => $this->view('admin/notices/upgrade-notice', [
                'dashboardUrl' => $this->env->getUrl('plugin.dashboard_url'),
            ]),
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

        $nonce = $this->request->get('global.' . $this->formNonce);
        if (wp_verify_nonce($nonce, $this->formAction) === false) {
            return;
        }

        delete_option('metricool_show_upgrade_notice');
    }

    /**
     * Determine if the upgrade notice should be shown.
     */
    private function shouldShowUpgradeNotice(): bool
    {
        $screen = get_current_screen();
        return get_option('metricool_show_upgrade_notice', false) && $screen->id !== 'toplevel_page_metricool';
    }
}
