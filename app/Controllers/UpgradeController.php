<?php

declare(strict_types=1);

namespace Metricool\Controllers;

use Metricool\Interfaces\ControllerInterface;
use Metricool\Support\Helpers\Storages\EnvironmentConfig;

class UpgradeController implements ControllerInterface
{
    private const LEGACY_VERSION = '1.24';

    private EnvironmentConfig $env;

    public function __construct(EnvironmentConfig $env)
    {
        $this->env = $env;
    }

    public function register(): void
    {
        add_action('metricool_controllers_loaded', [$this, 'checkForUpgrades']);
    }

    /**
     * Fire an action when the plugin is upgraded from one version to another.
     *
     * @internal Note the starting underscore in the option name. This is to
     * prevent the option from being deleted when a user logs out. As if
     * it is a private Metricool option.
     *
     * @hooked metricool_controllers_loaded to make sure Controllers can hook
     * into metricool_plugin_version_upgrade. Even this one.
     *
     * @uses do_action metricool_plugin_version_upgrade Only hook into this
     * action if the upgrade script is related to the responsibility of the
     * hooking class.
     */
    public function checkForUpgrades(): void
    {
        $previousSavedVersion = (string) get_option('_metricool_current_version', '');
        if ($previousSavedVersion === $this->env->getString('plugin.version')) {
            return; // Nothing to do
        }

        // This could be one if-statement, but this makes it readable that we
        // do not query the database if we do not need to.
        if (empty($previousSavedVersion)) {
            if ($this->isUpgradeFromLegacy()) {
                $previousSavedVersion = self::LEGACY_VERSION;
            }
        }

        // Trigger upgrade hook if we are upgrading from a previous version.
        if (!empty($previousSavedVersion)) {
            do_action('metricool_plugin_version_upgrade', $previousSavedVersion, $this->env->getString('plugin.version'));
        }

        // Also makes sure $previousSavedVersion will only be empty one time
        update_option('_metricool_current_version', $this->env->getString('plugin.version'), false);
    }

    /**
     * Check if the plugin is being upgraded from a legacy version.
     * @internal Ideally this method should be removed in the future.
     * @since 2.0.0
     */
    private function isUpgradeFromLegacy(): bool
    {
        if ($cache = wp_cache_get('metricool_was_legacy_plugin_active', 'metricool')) {
            return $cache;
        }

        $legacyProfileID = get_option('metricool_profile_id', '');
        $upgradeWasFromLegacy = !empty($legacyProfileID);

        wp_cache_set('metricool_was_legacy_plugin_active', $upgradeWasFromLegacy, 'metricool');
        return $upgradeWasFromLegacy;
    }
}
