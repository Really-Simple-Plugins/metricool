<?php

declare(strict_types=1);

namespace Metricool\Features\Onboarding;

use GuzzleHttp\Exception\GuzzleException;
use Metricool\Http\Metricool\MetricoolApi;
use Metricool\Services\TrackingScriptService;
use Metricool\Traits\HasRestAccess;

class OnboardingService
{
    use HasRestAccess;

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
    public function setOnboardingCompleted(): bool
    {
        $completedPreviously = get_option('metricool_onboarding_completed', false);
        if ($completedPreviously) {
            return true;
        }

        update_option('metricool_onboarding_completed_unix_timestamp', time(), false);
        return update_option('metricool_onboarding_completed', true, false);
    }


    /**
     * Check if there is only one brand connected to the blog, store it.
     * Returns false when it could not store the necessary information.
     * @throws GuzzleException when user has no access to the brand
     */
    public function findBlogAndStore(array $brands): bool
    {
        if (empty($brands)) {
            throw new \RuntimeException('Something went wrong. No blogs found.');
        }

        // Can't store brand information if there are more than one brand
        if (count($brands) !== 1) {
            return false;
        }

        return $this->storeBlogInfo($brands[0]['id']);
    }


    /**
     * Store the necessary onboarding information from the brand in the database
     * @throws GuzzleException when user has no access to the brand
     */
    public function storeBlogInfo(string $blogId): bool
    {
        // Attempt to get the brand information from the API, checks if the user can access it
        $brand = $this->api->brands()->get($blogId);

        // Store the blog id
        $this->api->storeBlogId((string) $brand['id']);

        // Store the tracking hash
        if (!empty($brand['hash'])) {
            $this->tracking->storeTrackingHash((string) $brand['hash']);
        }

        return true;
    }
}
