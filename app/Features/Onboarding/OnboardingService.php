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
     * @throws BrandAccessDeniedException
     */
    public function finalizeOnboarding(?string $blogId = null): bool
    {
        if ($blogId !== null) {
            // When a blogId is provided, try to connect to the brand
            $this->connectBlogId($blogId);
        } else {
            // Try to find the blog from the brands connected to the account and retrieve the necessary information
            $this->attemptToFindBlogIdFromApi();
        }

        // If the blogId is not set, the onboarding is not completed
        if ($this->api->hasBlogId() === false) {
            return false;
        }

        // todo: REMOVED EXCEPTION WHEN THIS BRAND RETURNS 403 FOR TESTING -> We should show an error that the tracker couldn't be loaded, or add the exception back.
        $this->maybeActivateTrackingHash($this->api->getBlogId());

        // When all the necessary information is retrieved, set the onboarding as completed
        return $this->dashboard->setOnboardingCompleted();
    }

    /**
     * Automatically find the blog from the connected brand and try to retrieve
     * the necessary onboarding information
     *
     * @throws BrandAccessDeniedException when the current user has no access to the brand
     */
    private function attemptToFindBlogIdFromApi(): void
    {
        try {
            $brands = $this->api->brands()->all();
        } catch (GuzzleException $e) {
            return;
        }

        $brand = (count($brands) === 1 ? (array) $brands[0] : []);
        if (empty($brand['id'])) {
            return;
        }

        $this->connectBlogId((string) $brand['id']);
    }

    /**
     * Store the necessary onboarding information from the Metricool brand
     */
    private function connectBlogId(string $blogId): void
    {
        $this->api->storeBlogId($blogId);
    }

    /**
     * Activate the tracking hash for the given blog ID and store it in the database
     */
    private function maybeActivateTrackingHash(string $blogId): void
    {
        try {
            $brand = $this->api->brands()->get($blogId);
        } catch (GuzzleException $e) {
            return;
        }

        $trackingId = isset($brand['hash']) ? (string) $brand['hash'] : null;

        if ($trackingId !== null) {
            $this->tracking->storeTrackingHash($trackingId)
                ->activateTrackingWidget();
        }
    }
}
