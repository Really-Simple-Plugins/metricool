<?php
namespace Metricool\Controllers;

use Metricool\Interfaces\ControllerInterface;

class SettingsController implements ControllerInterface
{
    public function register(): void
    {
        add_action('metricool_activation', [$this, 'handlePluginActivation']);
        add_action('metricool_plugin_version_upgrade', [$this, 'handlePluginUpgrade'], 10, 2);
    }

    /**
     * Handle plugin activation
     */
    public function handlePluginActivation(): void
    {
        // todo - add defaults
    }

    /**
     * Handle plugin upgrades
     */
    public function handlePluginUpgrade(string $previousVersion, string $newVersion): void
    {
        // If someone upgrades from legacy version we need to upgrade the
        // existing options
        if ($previousVersion && version_compare($previousVersion, '2.0', '<')) {
            // todo - I dont think we have to migrate the previous setting.
            // todo - There was only one setting which was stored in the option
            // todo - 'metricool_profile_id' and we can keep using that.
        }
    }
}