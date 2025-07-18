<?php
namespace Metricool\Http\Endpoints;

use Metricool\App;
use Metricool\Traits\HasRestAccess;
use Metricool\Traits\HasAllowlistControl;
use Metricool\Interfaces\SingleEndpointInterface;

class StatisticsEndpoint implements SingleEndpointInterface
{
    use HasRestAccess;
    use HasAllowlistControl;

    const ROUTE = 'statistics';

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
        return self::ROUTE . '/(?P<statistic>[^/]+)';
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
        $metric = $request->get_param('statistic') ?: '';
        $statisticsModule = App::provide('client')->statistics();

        if (!method_exists($statisticsModule, $metric)) {
            return $this->sendHttpResponse([], false, esc_html__('Unknown metric requested', 'metricool'), 400);
        }

        try {
            $metricModule = $statisticsModule->$metric();

            $requestFilters = $request->get_param('filters');
            if (method_exists($metricModule, 'filter') && !empty($requestFilters)) {
                $metricModule->filter($requestFilters);
            }

            $response = $metricModule->get();
        } catch (\Throwable $e) {
            echo '<pre>';
            var_dump($e->getMessage()); // todo
            exit();
        }

        return $this->sendHttpResponse($response);
    }
}