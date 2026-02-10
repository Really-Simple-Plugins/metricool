<?php

declare(strict_types=1);

namespace Metricool\Features\Onboarding;

use Metricool\Http\Metricool\MetricoolApi;
use Metricool\Services\TrackingScriptService;
use Metricool\Support\Helpers\Storage;
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

    public function isOnboardingCompleted(): bool
    {
        return get_option('metricool_onboarding_completed', false);
    }

    /**
     * Store the onboarding step in the general options without autoload
     */
    public function setCompletedStep(int $step): void
    {
        update_option('metricool_completed_step', $step, false);
    }

    /**
     * Set the onboarding as completed in the general options without autoload
     */
    public function setOnboardingCompleted(): bool
    {
        $this->setCompletedStep(5); // todo
        $this->clearTemporaryData();

        $completedPreviously = get_option('metricool_onboarding_completed', false);
        if ($completedPreviously) {
            return true;
        }

        update_option('metricool_onboarding_completed_unix_timestamp', time(), false);
        return update_option('metricool_onboarding_completed', true, false);
    }

    /**
     * Method can be used to set temporary data for the onboarding process.
     */
    public function setTemporaryData(array $data): void
    {
        $options = get_option('metricool_temporary_onboarding_data', []);
        $options = array_merge($options, $data);
        update_option('metricool_temporary_onboarding_data', $options, false);
    }

    /**
     * Method can be used to retrieve temporary data for the onboarding process.
     * Returns the array of data as a Storage object for easier access.
     */
    public function getTemporaryDataStorage(): Storage
    {
        return new Storage(
            get_option('metricool_temporary_onboarding_data', [])
        );
    }

    /**
     * Method should be used to clear the temporary data for the onboarding.
     */
    public function clearTemporaryData(): void
    {
        delete_option('metricool_temporary_onboarding_data');
    }

    /**
     * Check if there is only one brand connected to the blog, store it.
     * Returns false when it could not store the necessary information.
     */
    public function attemptToStoreBlogInfo(array $brands): bool
    {
        if (empty($brands)) {
            throw new \RuntimeException('Something went wrong. No blogs found.');
        }

        // Can't store brand information if there are more than one brand
        if (count($brands) !== 1) {
            return false;
        }

        // Attempt to get the brand information from the API, return false if it fails
        try {
            $brand = $this->api->brands()->get((string) $brands[0]['id']);
        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
            return false;
        }

        $this->storeBlogInfo($brand);

        return true;
    }


    /**
     * Store the necessary onboarding information from the brand in the database
     */
    public function storeBlogInfo(array $brand): void
    {
        // Store the blog id
        if (isset($brand['id'])) {
            $this->api->storeBlogId((string) $brand['id']);
        }

        // Store the tracking hash
        if (isset($brand['hash'])) {
            $this->tracking->storeTrackingHash((string) $brand['hash']);
        }
    }
}
