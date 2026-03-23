<?php

declare(strict_types=1);

namespace Metricool\Features\Onboarding;

use GuzzleHttp\Exception\GuzzleException;
use Metricool\Features\Onboarding\Exceptions\BrandAccessDeniedException;
use Metricool\Http\Metricool\MetricoolApi;
use Metricool\Services\TrackingScriptService;

class OnboardingService
{
    private MetricoolApi $api;
    private TrackingScriptService $tracking;

    public function __construct(MetricoolApi $api, TrackingScriptService $tracking)
    {
        $this->api = $api;
        $this->tracking = $tracking;
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
    public static function isOnboardingCompleted(): bool
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

    /**
     * Automatically find the blog from the connected brand and try to retrieve
     * the necessary onboarding information
     */
    public function finalizeOnboarding(): bool
    {
        try {
            $brands = $this->api->brands()->all();
        } catch (GuzzleException $e) {
            return false;
        }

        if (empty($brands)) {
            return false;
        }

        if (count($brands) > 1) {
            return false;
        }

        try {
            $this->storeBlogInfo((string) $brands[0]['id']);
        } catch (GuzzleException | BrandAccessDeniedException $e) {
            return false;
        }

        // When all the necessary information is retrieved, set the onboarding as completed
        return $this->setOnboardingCompleted();
    }

    /**
     * Store the necessary onboarding information from the Metricool brand
     *
     * @throws BrandAccessDeniedException when the current user has no access to the brand
     * @throws GuzzleException when the Metricool API request fails
     */
    public function storeBlogInfo(string $blogId): bool
    {
        // Attempt to get the brand information from the API, checks if the user can access it
        try {
            $brand = $this->api->brands()->get($blogId);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            // If user has no access to the brand, return an exception
            if ($e->getResponse()->getStatusCode() === 403) {
                throw new BrandAccessDeniedException();
            }
        }

        // Store the blog id
        if (isset($brand['id'])) {
            $this->api->storeBlogId((string) $brand['id']);
        } else {
            throw new \RuntimeException('Something went wrong.');
        }

        // Store the tracking hash
        if (! empty($brand['hash'])) {
            $this->tracking->storeTrackingHash((string) $brand['hash']);
            $this->tracking->activateTrackingWidget();
        }

        return true;
    }
}
