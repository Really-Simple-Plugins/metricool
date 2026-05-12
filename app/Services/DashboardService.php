<?php

namespace Metricool\Services;

use Metricool\Controllers\LegacyUpgradeController;
use Metricool\Http\Metricool\MetricoolApi;

class DashboardService
{
    public const ONBOARDING_COMPLETED_OPTION = 'metricool_onboarding_completed';
    public const FORCED_LOGIN_OPTION = 'metricool_force_login';

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
        return $this->api->hasUserToken() && $this->api->hasBlogId() && get_option(self::ONBOARDING_COMPLETED_OPTION, false);
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
        return update_option(self::ONBOARDING_COMPLETED_OPTION, time());
    }

    /**
     * Check if the front-end should show the forced login screen
     */
    public function isForcedLogin(): bool
    {
        return (bool) get_option(self::FORCED_LOGIN_OPTION, false);
    }

    /**
     * Set the forced login state
     */
    public function setForcedLogin(bool $forcedLogin): bool
    {
        return update_option(self::FORCED_LOGIN_OPTION, $forcedLogin);
    }

    /**
     * Set the forced login state
     */
    public function clearForcedLogin(): bool
    {
        return delete_option(self::FORCED_LOGIN_OPTION);
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
