<?php

namespace Metricool\Http\Endpoints;

use Metricool\App;
use Metricool\Http\Endpoints\Responses\AnalyticsResponse;
use Metricool\Interfaces\SingleEndpointInterface;
use Metricool\Services\AnalyticsService;
use Metricool\Traits\HasAllowlistControl;
use Metricool\Traits\HasRestAccess;

class AnalyticsEndpoint implements SingleEndpointInterface
{
    use HasRestAccess;
    use HasAllowlistControl;

    public const ROUTE = 'analytics';
    public AnalyticsService $service;

    public function __construct(AnalyticsService $service)
    {
        $this->service = $service;
    }

    /**
     * Only enable this endpoint if the user has access to the admin area and
     * the user has saved a user token, - ID and blog ID.
     */
    public function enabled(): bool
    {
        return $this->adminAccessAllowed() && App::provide('client')->hasAuthentication();
    }

    /**
     * @inheritDoc
     */
    public function registerRoute(): string
    {
        return self::ROUTE;
    }

    /**
     * @inheritDoc
     */
    public function registerArguments(): array
    {
        return [
            'methods' => \WP_REST_Server::READABLE,
            'callback' => [$this, 'callback'],
        ];
    }

    /**
     * Method will dynamically request the requested statistic. If the metric
     * is filterable and filters are provided, it will apply them before
     * retrieving the data.
     * @example /wp-json/metricool/v1/analytics
     */
    public function callback(\WP_REST_Request $request): \WP_REST_Response
    {
        try {
            $response = $this->buildResponse($request);
        } catch (\Exception $e) {
            return $this->sendHttpErrorResponse(esc_html__('Failed to load Analytics data', 'metricool'), $e->getMessage());
        }

        return $this->sendHttpResponse($response);
    }

    /**
     * Build the specific Analytics response for the endpoint. This is mainly
     * used in the plugin Dashboard to reflect non-realtime statistics.
     * Building it server side prevents client-side complexity.
     */
    private function buildResponse(\WP_REST_Request $request): array
    {
        $statisticsModule = App::provide('client')->statistics();
        $requestFilters = ($request->get_param('filters') ?: []);
        $requestMetrics = ($request->get_param('metrics') ?: ['pageViews', 'visits', 'visitors', 'posts', 'comments']);

        $this->service->setRequestFilters($requestFilters);

        if (in_array('pageViews', $requestMetrics)) {
            $this->service->loadMetric('pageViews', esc_html__('Page views', 'metricool'), $statisticsModule->pageViews());
        }

        if (in_array('visits', $requestMetrics)) {
            $this->service->loadMetric('visits', esc_html__('Visits', 'metricool'), $statisticsModule->visits());
        }

        if (in_array('visitors', $requestMetrics)) {
            $this->service->loadMetric('visitors', esc_html__('Visitors', 'metricool'), $statisticsModule->visitors());
        }

        if (in_array('posts', $requestMetrics)) {
            $this->service->loadMetric('posts', esc_html__('Posts', 'metricool'), $statisticsModule->posts());
        }

        if (in_array('comments', $requestMetrics)) {
            $this->service->loadMetric('comments', esc_html__('Comments', 'metricool'), $statisticsModule->comments());
        }

        $response = new AnalyticsResponse();
        $response->setTotals($this->service->getTotals());
        $response->setTimelineData($this->service->getTimelineData());

        return $response->body();
    }

}