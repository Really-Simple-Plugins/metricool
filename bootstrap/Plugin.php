<?php

declare(strict_types=1);

namespace Metricool\Bootstrap;

use Metricool\Http;
use Metricool\Providers;
use Metricool\Controllers;
use Metricool\Managers\FeatureManager;
use Metricool\Managers\EndpointManager;
use Metricool\Managers\ProviderManager;
use Metricool\Managers\ControllerManager;

class Plugin
{
    private App $app;
    private FeatureManager $featureManager;
    private ProviderManager $providerManager;
    private EndpointManager $endpointManager;
    private ControllerManager $controllerManager;

    /**
     * Plugin constructor
     */
    public function __construct()
    {
        $this->app = App::getInstance();

        $this->featureManager = new FeatureManager($this->app);
        $this->providerManager = new ProviderManager($this->app);
        $this->controllerManager = new ControllerManager($this->app);
        $this->endpointManager = new EndpointManager($this->app);
    }

    /**
     * Boot the plugin
     */
    public function boot(): void
    {
        $this->registerEnvironment();

        $pluginBaseFile = (plugin_basename(dirname(__DIR__)) . DIRECTORY_SEPARATOR . plugin_basename(dirname(__DIR__)) . '.php');
        register_activation_hook($pluginBaseFile, [$this, 'activation']);
        register_deactivation_hook($pluginBaseFile, [$this, 'deactivation']);
        register_uninstall_hook($pluginBaseFile, 'Metricool\Bootstrap\Plugin::uninstall');

        add_action('plugins_loaded', [$this, 'loadPluginTextDomain']);
        add_action('plugins_loaded', [$this, 'registerProviders']); // Provide functionality to the plugin
        add_action('metricool_providers_loaded', [$this->featureManager, 'registerFeatures']); // Makes sure features exist when Controllers need them
        add_action('metricool_features_loaded', [$this, 'registerControllers']); // Control the functionality of the plugin
        add_action('metricool_controllers_loaded', [$this, 'checkForUpgrades']); // Makes sure Controllers can hook into the upgrade process
        add_action('rest_api_init', [$this, 'registerEndpoints']);
        add_action('admin_init', [$this, 'fireActivationHook']);
    }

    /**
     * Register the plugin environment. The value of the environment will
     * determine which domain and app_key are used for the API calls. The
     * default value is production and can be [production|development].
     * See {@see config/environment.php} for the actual values.
     */
    public function registerEnvironment(): void
    {
        if (!defined('METRICOOL_ENV')) {
            define('METRICOOL_ENV', 'production');
        }
    }

    /**
     * Load the plugin text domain for translations
     */
    public function loadPluginTextDomain(): void
    {
        load_plugin_textdomain('metricool');
    }

    /**
     * Method that fires on activation. It creates a flag in the database
     * options table to indicate that the plugin is being activated. Flag is
     * used by {@see fireActivationHook} to run the activation hook only once.
     */
    public function activation(): void
    {
        global $pagenow;

        // Set the flag on activation
        update_option('metricool_activation_flag', true, false);
        update_option('metricool_activation_source_page', sanitize_text_field($pagenow), false);

        // Flush rewrite rules to ensure the new routes are available
        add_action('shutdown', 'flush_rewrite_rules');
    }

    /**
     * Method fires the activation hook. But only if the plugin is being
     * activated. The flag is set in the database options table
     * {@see activation} and is used to determine if the plugin is being
     * activated. This method removes the flag after it has been used.
     */
    public function fireActivationHook(): void
    {
        if (get_option('metricool_activation_flag', false) === false) {
            return;
        }

        // Get the source page where the activation was triggered from
        $source = get_option('metricool_activation_source_page', 'unknown');

        // Remove the activation flag so the action doesn't run again. Do it
        // before the action so its deleted before anything can go wrong.
        delete_option('metricool_activation_flag');
        delete_option('metricool_activation_source_page');

        // Gives possibility to hook into the activation process
        do_action('metricool_activation', $source); // !important
    }

    /**
     * Method that fires on deactivation
     */
    public function deactivation(): void
    {
        // Silence is golden
    }

    /**
     * Method that fires on uninstall
     */
    public static function uninstall(): void
    {
        $uninstallInstance = new \Metricool\Support\Helpers\Uninstall();
        $uninstallInstance->handlePluginUninstall();
    }

    /**
     * Register Plugin providers. First step in the booting process of the
     * plugin. Is hooked into plugins_loaded to make sure we only boot the
     * plugin after all other plugins are loaded. This plugin depends on the
     * providerManager to fire the metricool_providers_loaded action.
     * @uses do_action metricool_providers_loaded
     */
    public function registerProviders(): void
    {
        $this->providerManager->register([
            Providers\ConfigServiceProvider::class,
            Providers\RequestServiceProvider::class,
            Providers\ClientServiceProvider::class,
        ]);
    }

    /**
     * Register Controllers. Hooked into metricool_features_loaded to make sure
     * features are available to the Controllers.
     * @uses do_action metricool_controllers_loaded
     */
    public function registerControllers(): void
    {
        $this->controllerManager->register([
            Controllers\AdminController::class,
            Controllers\DashboardController::class,
            Controllers\SettingsController::class,
            Controllers\CapabilityController::class,
            Controllers\ReviewController::class,
            Controllers\TrackingScriptController::class,
            Controllers\SharePostController::class,
        ]);
    }

    /**
     * Register the plugins REST API endpoint instances. Hooked into
     * rest_api_init to make sure the REST API is available.
     * @uses do_action metricool_endpoints_loaded
     */
    public function registerEndpoints(): void
    {
        $this->endpointManager->register([
            Http\Endpoints\ConnectedBrandsEndpoint::class,
            Http\Endpoints\ConnectedNetworksEndpoint::class,
            Http\Endpoints\SubscriptionEndpoint::class,
            Http\Endpoints\DistributionEndpoint::class,
            Http\Endpoints\AnalyticsEndpoint::class,
            Http\Endpoints\RealtimeEndpoint::class,
            Http\Endpoints\RelatedPluginsEndpoints::class,
        ]);
    }

    /**
     * Fire an action when the plugin is upgraded from one version to another.
     * Hooked into metricool_controllers_loaded to make sure Controllers can
     * hook into metricool_plugin_version_upgrade.
     *
     * @internal Note the starting underscore in the option name. This is to
     * prevent the option from being deleted when a user logs out. As if
     * it is a private Metricool option.
     *
     * @uses do_action metricool_plugin_version_upgrade
     */
    public function checkForUpgrades(): void
    {
        $previousSavedVersion = (string) get_option('_metricool_current_version', '');
        if ($previousSavedVersion === $this->app->env->getString('plugin.version')) {
            return; // Nothing to do
        }

        // This could be one if-statement, but this makes it readable that we
        // do not query the database if we do not need to.
        if (empty($previousSavedVersion)) {
            if ($this->isUpgradeFromLegacy()) {
                $previousSavedVersion = '1.24';
            }
        }

        // Trigger upgrade hook if we are upgrading from a previous version.
        // Action can be used by Controllers to hook into the upgrade process
        if (!empty($previousSavedVersion)) {
            do_action('metricool_plugin_version_upgrade', $previousSavedVersion, $this->app->env->getString('plugin.version'));
        }

        // Also makes sure $previousSavedVersion will only be empty one time
        update_option('_metricool_current_version', $this->app->env->getString('plugin.version'), false);
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
