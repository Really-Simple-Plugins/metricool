<?php

namespace Metricool\Features\Onboarding;

use GuzzleHttp\Exception\GuzzleException;
use Metricool\Features\Onboarding\Exceptions\BrandAccessDeniedException;
use Metricool\Http\Metricool\MetricoolApi;
use Metricool\Services\DashboardService;
use Metricool\Services\TrackingScriptService;

exit('D');

class OnboardingService
{
    private MetricoolApi $api;
    private TrackingScriptService $tracking;
    private DashboardService $dashboard;

    public function __construct(MetricoolApi $api, TrackingScriptService $tracking, DashboardService $dashboard)
    {
        $this->api = $api;
        $this->tracking = $tracking;
        $this->dashboard = $dashboard;
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
        return $this->dashboard->setOnboardingCompleted();
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
            $this->tracking->activateTrackingWidget((string) $brand['hash']);
        }

        return true;
    }
}
