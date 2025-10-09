<?php
namespace Metricool\Http\Endpoints;

use Carbon\Carbon;
use Metricool\App;
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

        $pageViews = $statisticsModule->pageViews();
        $visits = $statisticsModule->visits();
        $visitors = $statisticsModule->visitors();
        $posts = $statisticsModule->posts();
        $comments = $statisticsModule->comments();

        // ASK: code style ok?
        $startDate = isset($requestFilters['start']) ?
            Carbon::createFromFormat('Ymd', $requestFilters['start']) :
            Carbon::now()->subDays(30);

        $endDate = isset($requestFilters['end']) ?
            Carbon::createFromFormat('Ymd', $requestFilters['end']) :
            Carbon::now()->subDays(30);

        $this->analyticsService
            ->setStartDate($startDate)
            ->setEndDate($endDate)
            ->addStatistic('pageViews', $pageViews)
            ->addStatistic('visits', $visits)
            ->addStatistic('visitors', $visitors)
            ->addStatistic('posts', $posts)
            ->addStatistic('comments', $comments);

        return [
            'totals' => [
                'pageViews' => [
                    'totalAmount' => $this->analyticsService->getTotalAmount('pageViews'),
                    'trend' => $this->analyticsService->getTrend('pageViews'),
                ],
                'visits' => [
                    'totalAmount' => $this->analyticsService->getTotalAmount('visits'),
                    'trend' => $this->analyticsService->getTrend('visits'),
                ],
                'visitors' => [
                    'totalAmount' => $this->analyticsService->getTotalAmount('visitors'),
                    'trend' => $this->analyticsService->getTrend('visitors'),
                ],
                'posts' => [
                    'totalAmount' => $this->analyticsService->getTotalAmount('posts'),
                    'trend' => $this->analyticsService->getTrend('posts'),
                ],
                'comments' => [
                    'totalAmount' => $this->analyticsService->getTotalAmount('comments'),
                    'trend' => $this->analyticsService->getTrend('comments'),
                ],
            ],
            'timelineData' => $this->analyticsService->createTimeLine()
        ];

    }
}