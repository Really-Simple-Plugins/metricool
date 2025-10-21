<?php
namespace Metricool\Http\Endpoints;

use Metricool\App;
use Metricool\Http\Responses\AnalyticsResponse;
use Metricool\Services\AnalyticsService;
use Metricool\Traits\HasRestAccess;
use Metricool\Traits\HasAllowlistControl;
use Metricool\Interfaces\SingleEndpointInterface;

class AnalyticsEndpoint implements SingleEndpointInterface
{
    use HasRestAccess;
    use HasAllowlistControl;

    const ROUTE = 'analytics';

    protected AnalyticsService $analyticsService;

    public function __construct(AnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
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
            return $this->sendHttpErrorResponse(esc_html__('Failed to load analytics data', 'metricool'), $e->getMessage(), 500);
        }

        return $this->sendHttpResponse($response);
    }

    /**
     * Build the specific analytics response for the endpoint. This is mainly
     * used in the plugin Dashboard to reflect non-realtime statistics.
     * Building it server side prevents client-side complexity.
     */
    private function buildResponse(\WP_REST_Request $request): array
    {
        $statisticsModule = App::provide('client')->statistics();
        $requestFilters = ($request->get_param('filters') ?: []);

        $response = new AnalyticsResponse($requestFilters);

        $response->addMetric('pageViews', esc_html__('Page views', 'metricool'), $statisticsModule->pageViews());
        $response->addMetric('visits', esc_html__('Visits', 'metricool'), $statisticsModule->visits());
        $response->addMetric('visitors', esc_html__('Visitors', 'metricool'), $statisticsModule->visitors());
        $response->addMetric('posts', esc_html__('Posts', 'metricool'), $statisticsModule->posts());
        $response->addMetric('comments', esc_html__('Comments', 'metricool'), $statisticsModule->comments());

        return $response->body();

    }
}