<?php
namespace Metricool\Http\Endpoints;

use Metricool\App;
use Metricool\Traits\HasRestAccess;
use Metricool\Services\AnalyticsService;
use Metricool\Traits\HasAllowlistControl;
use Metricool\Interfaces\SingleEndpointInterface;

class AnalyticsEndpoint implements SingleEndpointInterface
{
    use HasRestAccess;
    use HasAllowlistControl;

    const ROUTE = 'analytics';

    protected AnalyticsService $service;

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
     * @example /wp-json/metricool/v1/statistics/countries?filters[start]=20250618&filters[end]=20250718&filters[country]=nl
     */
    public function callback(\WP_REST_Request $request): \WP_REST_Response
    {
        try {
            $response = $this->buildResponse($request);
        } catch (\Throwable $e) {
            echo '<pre>';
            var_dump($e->getMessage()); // todo
            exit();
        }

        return $this->sendHttpResponse($response);
    }

    /**
     * Build the specific analytics response for the endpoint. This is mainly
     * used in the plugin Dashboard to reflect non-realtime statistics.
     * Building it server side prevents client-side complexity.
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function buildResponse(\WP_REST_Request $request): array
    {
        $statisticsModule = App::provide('client')->statistics();
        $requestFilters = ($request->get_param('filters') ?: []);

        return [
            'pageViews' => [
                'label' => esc_html__('Page views', 'metricool'),
                'data' => $statisticsModule->pageViews()->filter($requestFilters)->get(),
                'trend' => $this->service->getTrend($statisticsModule->pageViews(), $requestFilters),
            ],
            'visits' => [
                'label' => esc_html__('Visits', 'metricool'),
                'data' => $statisticsModule->visits()->filter($requestFilters)->get(),
                'trend' => $this->service->getTrend($statisticsModule->visits(), $requestFilters),
            ],
            'visitors' => [
                'label' => esc_html__('Visitors', 'metricool'),
                'data' => $statisticsModule->visitors()->filter($requestFilters)->get(),
                'trend' => $this->service->getTrend($statisticsModule->visitors(), $requestFilters),
            ],
            'posts' => [
                'label' => esc_html__('Posts', 'metricool'),
                'data' => $statisticsModule->posts()->filter($requestFilters)->get(),
                'trend' => $this->service->getTrend($statisticsModule->posts(), $requestFilters),
            ],
            'comments' => [
                'label' => esc_html__('Comments', 'metricool'),
                'data' => $statisticsModule->comments()->filter($requestFilters)->get(),
                'trend' => $this->service->getTrend($statisticsModule->comments(), $requestFilters),
            ],
        ];
    }
}