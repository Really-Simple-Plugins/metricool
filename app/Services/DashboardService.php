<?php

namespace Metricool\Services;

use Metricool\Http\Metricool\MetricoolApi;

class DashboardService
{
    private MetricoolApi $api;

    public function __construct(MetricoolApi $api)
    {
        $this->api = $api;
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
            'completed' => $this->isOnboardingCompleted(), // When the onboarding is completed, the user can start using the plugin
            'authenticated' => $this->api->hasUserToken(), // When the user is authenticated, the plugin can retrieve the necessary information from the Metricool API
            'blog_id_selected' => $this->api->hasBlogId(), // When the user has selected a blog, the plugin has stored the blog id
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
        return get_option('metricool_onboarding_completed', false) !== false;
    }

    /**
     * Store the onboarding timestamp
     */
    public function setOnboardingCompleted(bool $completed = true): bool
    {
        if ($completed === false) {
            return delete_option('metricool_onboarding_completed');
        }

        return update_option('metricool_onboarding_completed', time(), false);
    }

    /**
     * Check if the onboarding was completed from the legacy plugin
     */
    public function isFromLegacyPlugin(): bool
    {
        return (bool) get_option('metricool_from_legacy_plugin', false);
    }

    /**
     * Check if the welcome screen should be shown
     */
    public function shouldShowWelcomeScreen(): bool
    {
        return $this->isOnboardingCompleted() && $this->showWelcomeScreenOnce();
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
     * Set the welcome screen as shown
     */
    public function setShowWelcomeScreen(): void
    {
        update_option('metricool_show_welcome_screen', true);
    }
}
