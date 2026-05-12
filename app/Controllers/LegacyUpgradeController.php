<?php

declare(strict_types=1);

namespace Metricool\Controllers;

use Metricool\Interfaces\ControllerInterface;

class LegacyUpgradeController implements ControllerInterface
{
    public function register(): void
    {
        add_action('metricool_plugin_version_upgrade', [$this, 'maybeSetLegacyFlags'], 10, 2);
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
}
