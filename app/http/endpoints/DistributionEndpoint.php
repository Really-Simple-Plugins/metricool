<?php

namespace Metricool\Http\Endpoints;

use Metricool\App;
use Metricool\Http\Endpoints\Responses\Statistics\CountriesResponse;
use Metricool\Http\Endpoints\Responses\Statistics\RefererResponse;
use Metricool\Interfaces\SingleEndpointInterface;
use Metricool\Traits\HasAllowlistControl;
use Metricool\Traits\HasRestAccess;

class DistributionEndpoint implements SingleEndpointInterface
{
    use HasRestAccess;
    use HasAllowlistControl;

    const ROUTE = 'distribution';

    const metricsResponseMapper = [
        'countries' => CountriesResponse::class,
        'referers' => RefererResponse::class,
    ];

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
        return self::ROUTE . '/(?P<metric>[^/]+)';
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
        } catch (\Exception $e) {
            return $this->sendHttpErrorResponse(esc_html__('Failed to load Analytics data', 'metricool'), $e->getMessage(), 500);
        }

        return $this->sendHttpResponse($response);

    }

    /**
     * Build the specific Analytics response for the endpoint. This is mainly
     * used in the plugin Dashboard to reflect non-realtime statistics.
     * Building it server side prevents client-side complexity.
     *
     * @throws \Exception
     */
    private function buildResponse(\WP_REST_Request $request): array
    {
        $statisticsModule = App::provide('client')->statistics();

        $metric = $request->get_param('metric') ?: '';
        $requestFilters = $request->get_param('filters');

        $response = $this->findResponseFromMetric($metric);

        $metricModule = $statisticsModule->$metric();
        if (!empty($requestFilters)) {
            $metricModule->filter($requestFilters);
        }
        $results = $metricModule->get();

        $response = new $response($results);

        return $response->body();
    }

    protected function findResponseFromMetric($metric): string
    {
        if (!array_key_exists($metric, self::metricsResponseMapper)) {
            throw new \InvalidArgumentException("Metric $metric is not accepted by this endpoint");
        }
        return self::metricsResponseMapper[$metric];
    }
}