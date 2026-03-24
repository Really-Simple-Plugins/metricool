<?php

namespace Metricool\Features\Onboarding;

use GuzzleHttp\Exception\GuzzleException;
use Metricool\Features\Onboarding\Exceptions\BrandAccessDeniedException;
use Metricool\Http\Metricool\MetricoolApi;
use Metricool\Services\DashboardService;
use Metricool\Services\TrackingScriptService;

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
     * Attempt to finish the onboarding process. When the necessary information is provided or retrieved,
     * set the onboarding as completed.
     *
     * @throws GuzzleException
     * @throws BrandAccessDeniedException
     */
    public function finalizeOnboarding(?string $blogId = null): bool
    {
        // When a blogId is provided, try to connect to the brand
        if ($blogId !== null) {
            $this->connectBlogId($blogId);
        } else {
            // When no blogId is provided, try to find the blog from the connected brands
            $this->attemptToFindBlogIdFromApi();
        }

        // If the blogId is not set, the onboarding is not completed
        if ($this->api->hasBlogId() === false) {
            return false;
        }

        // When all the necessary information is retrieved, set the onboarding as completed
        $this->dashboard->setOnboardingCompleted();

        return true;
    }

    /**
     * Automatically find the blog from the connected brand and try to retrieve
     * the necessary onboarding information
     *
     * @throws BrandAccessDeniedException when the current user has no access to the brand
     */
    private function attemptToFindBlogIdFromApi(): bool
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
            $this->connectBlogId((string) $brands[0]['id']);
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
    private function connectBlogId(string $blogId): void
    {
        try {
            $brand = $this->api->brands()->get($blogId);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            if ($e->getResponse()->getStatusCode() === 403) {
                throw new BrandAccessDeniedException();
            }
        }

        // Store BlogId in API Client
        if (isset($brand['id'])) {
            $this->api->storeBlogId((string) $brand['id']);
        } else {
            throw new \RuntimeException('Something went wrong.');
        }

        // Store the tracking hash and active the tracking widget
        if (! empty($brand['hash'])) {
            $this->tracking->storeTrackingHash((string) $brand['hash']);
            $this->tracking->activateTrackingWidget();
        }
    }
}
