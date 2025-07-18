<?php
namespace Metricool\Http\Endpoints;

use Metricool\App;
use Metricool\Traits\HasRestAccess;
use Metricool\Traits\HasAllowlistControl;
use Metricool\Interfaces\MultiEndpointInterface;

class RealtimeEndpoint implements MultiEndpointInterface
{
    use HasRestAccess;
    use HasAllowlistControl;

    const ROUTE = 'realtime';

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
    public function registerRoutes(): array
    {
        return [
            self::ROUTE . '/(?P<statistic>[^/]+)' => [
                'methods' => \WP_REST_Server::READABLE,
                'callback' => [$this, 'callback'],
            ],
            self::ROUTE . '/(?P<statistic>[^/]+)/(?P<operation>[^/]+)' => [
                'methods' => \WP_REST_Server::READABLE,
                'callback' => [$this, 'callback'],
            ],
        ];
    }

    /**
     * Method will dynamically request the requested realtime statistic. Method
     * is used for both endpoints in this route. When called without an
     * operation, it defaults to 'get'.
     *
     * @example /wp-json/metricool/v1/realtime/countries (without operation)
     * @example /wp-json/metricool/v1/realtime/countries/get
     * @example /wp-json/metricool/v1/realtime/countries/count
     * @example /wp-json/metricool/v1/realtime/countries/sum
     */
    public function callback(\WP_REST_Request $request): \WP_REST_Response
    {
        $metric = $request->get_param('statistic') ?: '';
        $operation = $request->get_param('operation') ?: 'get';
        $realtimeModule = App::provide('client')->realtime();

        if (!method_exists($realtimeModule, $metric)) {
            return $this->sendHttpResponse([], false, esc_html__('Unknown metric requested', 'metricool'), 400);
        }

        if (!method_exists($realtimeModule->$metric(), $operation)) {
            return $this->sendHttpResponse([], false, esc_html__('Unknown operation requested', 'metricool'), 400);
        }

        try {
            $response = $realtimeModule->$metric()->$operation();
        } catch (\Throwable $e) {
            echo '<pre>';
            var_dump($e->getMessage()); // todo
            exit();
        }

        return $this->sendHttpResponse($response);
    }
}