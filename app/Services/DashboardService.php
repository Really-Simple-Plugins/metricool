<?php

namespace Metricool\Services;

use Metricool\Controllers\LegacyUpgradeController;
use Metricool\Http\Metricool\MetricoolApi;

class DashboardService
{
    private MetricoolApi $api;
    private LegacyUpgradeController $legacy;

    public function __construct(MetricoolApi $api, LegacyUpgradeController $legacy)
    {
        $this->api = $api;
        $this->legacy = $legacy;
    }

    /**
     * Returns the current state of the onboarding process
     * [
     *  'completed' => When the onboarding is completed, the user can start using the plugin
     *  'authenticated' => The plugin is authenticated with the Metricool API
     *  'blog_id_selected' => The plugin has stored the blog id and can retrieve the necessary information from the Metricool API
     * ]
     */
    public function state(): array
    {
        return [
            'completed' => $this->isOnboardingCompleted(),
            'authenticated' => $this->api->hasUserToken(),
            'blog_id_selected' => $this->api->hasBlogId(),
        ];
    }

    /**
     * Returns the current mode of the onboarding process
     * [
     *   'show_welcome_screen' => The welcome screen should be shown
     *   'forced_login' => Force the user to log in
     *  ]
     */
    public function mode(): array
    {
        return [
            'show_welcome_screen' => $this->shouldShowWelcomeScreen(),
            'forced_login' => $this->isFromLegacyPlugin(),
        ];
    }

    /**
     * Check if the onboarding was completed
     */
    public function isOnboardingCompleted(): bool
    {
        return $this->api->hasUserToken() && $this->api->hasBlogId() && get_option('metricool_onboarding_completed', false);
    }

    /**
     * Completes the onboarding process and store the timestamp
     */
    public function setOnboardingCompleted(): bool
    {
        // Remove the legacy flags
        // todo: use an event so this code can be moved to the LegacyController?
        $this->legacy->deleteLegacyFlags();

        // store the onboarding timestamp
        return update_option('metricool_onboarding_completed', time());
    }

    /**
     * Clear the onboarding data
     */
    public function clearOnboardingData()
    {
        delete_option('metricool_onboarding_completed');
    }

    /**
     * Check if the onboarding was completed from the legacy plugin
     */
    public function isFromLegacyPlugin(): bool
    {
        return (bool) get_option('metricool_from_legacy_plugin', false);
    }

    /**
     * Check if the welcome screen should be shown once
     */
    public function showWelcomeScreenOnce(): bool
    {
        $show = (bool) get_option('metricool_show_welcome_screen', false);
        delete_option('metricool_show_welcome_screen');

        return $show;
    }

    /**
     * Check if the welcome screen should be shown
     */
    public function shouldShowWelcomeScreen(): bool
    {
        return $this->isOnboardingCompleted() && $this->showWelcomeScreenOnce();
    }

    /**
     * Set the welcome screen as shown
     */
    public function setShowWelcomeScreen(): void
    {
        update_option('metricool_show_welcome_screen', true);
    }
}
