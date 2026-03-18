<?php

declare(strict_types=1);

namespace Metricool\Features\Onboarding;

use GuzzleHttp\Exception\GuzzleException;
use Metricool\Features\Onboarding\Exceptions\BrandAccessDeniedException;
use Metricool\Features\Onboarding\Exceptions\TooManyBrandsException;
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
     * Set the onboarding as completed in the general options without autoload
     */
    public function setOnboardingCompleted(): void
    {
        // set the timestamp
        update_option('metricool_onboarding_completed_unix_timestamp', time(), false);
        // set completed
        update_option('metricool_onboarding_completed', true, false);
    }


    /**
     * Automatically find the blog from the connected brand and try to retrieve
     * the necessary onboarding information
     *
     * @throws TooManyBrandsException when there are more than one brand connected to the blog
     * @throws BrandAccessDeniedException when the user does not have access to the picked brand
     */
    public function findAndRetrieveBlogInfo(array $brands): bool
    {
        if (empty($brands)) {
            throw new \RuntimeException('Something went wrong. No blogs found.');
        }

        // Can't store brand information if there are more than one brand
        if (count($brands) !== 1) {
            throw new TooManyBrandsException($brands);
        }

        try {
            $this->storeBlogInfo((string) $brands[0]['id']);
        } catch (GuzzleException $e) {
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

    public function isOnboardingCompleted(): bool
    {
        return (bool) get_option('metricool_onboarding_completed', false);
    }

    public function isFromLegacyPlugin(): bool
    {
        return (bool) get_option('metricool_from_legacy_plugin', false);
    }

    public function state(): array
    {
        return [
            'completed' => $this->isOnboardingCompleted(),
            'authenticated' => $this->api->hasUserToken(),
            'blog_id_selected' => $this->api->hasBlogId(),
            'forced_login' => $this->isFromLegacyPlugin(),
        ];
    }
}
