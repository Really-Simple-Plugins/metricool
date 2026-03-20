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

    public function state(): array
    {
        return [
            'completed' => $this->isOnboardingCompleted(),
            'authenticated' => $this->api->hasUserToken(),
            'blog_id_selected' => $this->api->hasBlogId(),
        ];
    }

    public function mode(): array
    {
        return [
            'show_welcome_screen' => $this->shouldShowWelcomeScreen(),
            'forced_login' => $this->isFromLegacyPlugin(),
        ];
    }

    public static function isOnboardingCompleted(): bool
    {
        return get_option('metricool_onboarding_completed', false) !== false;
    }

    /**
     * Set the onboarding as completed in the general options without autoload
     *
     */
    public function setOnboardingCompleted(bool $completed = true): void
    {
        if ($completed === false) {
            delete_option('metricool_onboarding_completed');
        } else {
            update_option('metricool_onboarding_completed', time(), false);
        }
    }

    public function isFromLegacyPlugin(): bool
    {
        return (bool) get_option('metricool_from_legacy_plugin', false);
    }

    public function shouldShowWelcomeScreen(): bool
    {
        return $this->isOnboardingCompleted() && $this->showWelcomeScreenOnce();
    }

    public function showWelcomeScreenOnce(): bool
    {
        $show = (bool) get_option('metricool_show_welcome_screen', false);
        $this->setShowWelcomeScreen(false);

        return $show;
    }

    public function setShowWelcomeScreen(bool $show = true): void
    {
        update_option('metricool_show_welcome_screen', $show, false);
    }

    /**
     * Automatically find the blog from the connected brand and try to retrieve
     * the necessary onboarding information
     */
    public function findAndRetrieveBlogInfo(): bool
    {
        try {
            $brands = $this->api->brands()->all();
        } catch (GuzzleException $e) {
            return false;
        }

        if (empty($brands)) {
            return false;
        }

        try {
            $this->storeBlogInfo((string) $brands[0]['id']);
        } catch (GuzzleException | BrandAccessDeniedException $e) {
            return false;
        }

        return true;
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
        }

        return true;
    }
}
