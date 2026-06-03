<?php

namespace Metricool\Features\Onboarding;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Metricool\Features\Onboarding\Exceptions\BrandAccessDeniedException;
use Metricool\Http\Metricool\MetricoolApi;
use Metricool\Services\DashboardService;
use Metricool\Services\MetricoolAccountService;
use Metricool\Services\TrackingScriptService;

class OnboardingService
{
    private MetricoolApi $api;
    private TrackingScriptService $tracking;
    private DashboardService $dashboard;
    private MetricoolAccountService $account;

    public function __construct(MetricoolApi $api, TrackingScriptService $tracking, DashboardService $dashboard, MetricoolAccountService $account)
    {
        $this->api = $api;
        $this->tracking = $tracking;
        $this->dashboard = $dashboard;
        $this->account = $account;
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
        }

        // If the blogId is not set, the onboarding is not completed
        if ($this->api->hasBlogId() === false) {
            return false;
        }

        // Update the metricool user data from the API
        $this->account->fetch();

        // When all the necessary information is retrieved, set the onboarding as completed
        return $this->dashboard->setOnboardingCompleted();
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
