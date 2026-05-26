<?php

namespace Metricool\Features\Onboarding;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Metricool\Features\Onboarding\Exceptions\BrandAccessDeniedException;
use Metricool\Http\Metricool\MetricoolApi;
use Metricool\Services\DashboardService;
use Metricool\Services\MetricoolUserService;
use Metricool\Services\TrackingScriptService;

class OnboardingService
{
    private MetricoolApi $api;
    private TrackingScriptService $tracking;
    private DashboardService $dashboard;
    private MetricoolUserService $metricoolUser;

    public function __construct(MetricoolApi $api, TrackingScriptService $tracking, DashboardService $dashboard, MetricoolUserService $metricoolUser)
    {
        $this->api = $api;
        $this->tracking = $tracking;
        $this->dashboard = $dashboard;
        $this->metricoolUser = $metricoolUser;
    }

    /**
     * Attempt to finish the onboarding process. When the necessary information is provided or retrieved,
     * set the onboarding as completed.
     *
     * @throws BrandAccessDeniedException
     * @throws GuzzleException
     */
    public function finalizeOnboarding(?string $blogId = null): bool
    {
        if ($blogId !== null) {
            // When a blogId is provided, try to connect to the brand
            $this->connectBrand($blogId);
        } else {
            // Try to find the blog from the brands connected to the account and retrieve the necessary information
            $this->attemptToFindBlogIdFromApi();
        }

        // If the blogId is not set, the onboarding is not completed
        if ($this->api->hasBlogId() === false) {
            return false;
        }

        // Update the metricool user data from the API
        $this->metricoolUser->update();

        // When all the necessary information is retrieved, set the onboarding as completed
        return $this->dashboard->setOnboardingCompleted();
    }

    /**
     * Automatically find the blog from the connected brand and try to retrieve
     * the necessary onboarding information
     *
     * @throws BrandAccessDeniedException when the current user has no access to the brand
     * @throws GuzzleException
     */
    private function attemptToFindBlogIdFromApi(): void
    {
        try {
            $brands = $this->api->brands()->all();
        } catch (GuzzleException $e) {
            return;
        }

        // Get the brand when there is only one, abort if there are more
        $brand = (count($brands) === 1 ? (array) $brands[0] : []);
        if (!isset($brand['id'])) {
            return;
        }

        $this->connectBrand((string) $brand['id']);
    }

    /**
     * A brand is connected when it's retrieved from the API and the tracking hash is activated. The blogId is stored for future API calls.
     * @throws GuzzleException
     */
    private function connectBrand(string $blogId): void
    {
        try {
            $brand = $this->api->brands()->get($blogId);
        } catch (RequestException $e) {
            if ($e->getResponse()->getStatusCode() === 403) {
                throw new BrandAccessDeniedException();
            }
            throw $e;
        }

        $this->activateTrackingHash($brand);
        $this->api->storeBlogId($blogId);
    }

    /**
     * Activate the tracking hash for the given brand and store it in the database
     */
    private function activateTrackingHash(array $brand): void
    {
        $trackingId = isset($brand['hash']) ? (string) $brand['hash'] : null;

        if ($trackingId !== null) {
            $this->tracking->storeTrackingHash($trackingId)
                ->activateTrackingWidget();
        }
    }
}
