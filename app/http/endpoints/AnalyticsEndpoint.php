<?php
namespace Metricool\Http\Endpoints;

use Metricool\App;
use Metricool\Services\Analytics\TimelineService;
use Metricool\Services\Analytics\TrendService;
use Metricool\Traits\HasRestAccess;
use Metricool\Traits\HasAllowlistControl;
use Metricool\Interfaces\SingleEndpointInterface;
use Metricool\Utility\ArrayUtility;

class AnalyticsEndpoint implements SingleEndpointInterface
{
    use HasRestAccess;
    use HasAllowlistControl;

    const ROUTE = 'analytics';

    protected trendService $trendService;
    protected timelineService $timelineService;

    public function __construct(TrendService $trendService, TimelineService $timelineService)
    {
        $this->trendService = $trendService;
        $this->timelineService = $timelineService;
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
        } catch (\Throwable $e) {
            return $this->sendHttpErrorResponse(__('Failed to load analytics data', 'metricool'), $e->getMessage(), 500);
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

        $pageViews = $statisticsModule->pageViews()->filter($requestFilters)->get();
        $visits = $statisticsModule->visits()->filter($requestFilters)->get();
        $visitors = $statisticsModule->visitors()->filter($requestFilters)->get();
        $posts = $statisticsModule->posts()->filter($requestFilters)->get();
        $comments = $statisticsModule->comments()->filter($requestFilters)->get();

        $statistics = ['pageViews', 'visits', 'visitors', 'posts', 'comments'];


        return [
            'totals' => [
                'pageViews' => [
                    'totalAmount' => ArrayUtility::sumValues(array_column($pageViews, 1)),
                    'trend' => $this->trendService->getTrend($statisticsModule->pageViews(), $requestFilters),
                ],
                'visits' => [
                    'totalAmount' => ArrayUtility::sumValues(array_column($visits, 1)),
                    'trend' => $this->trendService->getTrend($statisticsModule->visits(), $requestFilters),
                ],
                'visitors' => [
                    'totalAmount' => ArrayUtility::sumValues(array_column($visitors, 1)),
                    'trend' => $this->trendService->getTrend($statisticsModule->visitors(), $requestFilters),
                ],
                'posts' => [
                    'totalAmount' => ArrayUtility::sumValues(array_column($posts, 1)),
                    'trend' => $this->trendService->getTrend($statisticsModule->posts(), $requestFilters),
                ],
                'comments' => [
                    'totalAmount' => ArrayUtility::sumValues(array_column($comments, 1)),
                    'trend' => $this->trendService->getTrend($statisticsModule->comments(), $requestFilters),
                ],
            ],
            'timelineData' => $this->timelineService->createTimeLine([
                'pageViews' => $pageViews,
                'visits' => $visits,
                'visitors' => $visitors,
                'posts' => $posts,
                'comments' => $comments,
            ])
        ];
    }
}