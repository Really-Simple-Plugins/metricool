<?php

declare(strict_types=1);

namespace Metricool\Http\Endpoints;

use Metricool\Http\Metricool\MetricoolApi;
use Metricool\Interfaces\SingleEndpointInterface;
use Metricool\Services\DashboardService;
use Metricool\Support\Helpers\Storages\EnvironmentConfig;
use Metricool\Traits\DeletesOptions;
use Metricool\Traits\HasAllowlistControl;
use Metricool\Traits\HasRestAccess;

class LogoutEndpoint implements SingleEndpointInterface
{
    use HasRestAccess;
    use HasAllowlistControl;
    use DeletesOptions;

    public const ROUTE = 'logout';

    public MetricoolApi $metricoolApi;
    private EnvironmentConfig $env;
    private DashboardService $dashboard;

    public function __construct(MetricoolApi $metricoolApi, EnvironmentConfig $env, DashboardService $dashboard)
    {
        $this->metricoolApi = $metricoolApi;
        $this->env = $env;
        $this->dashboard = $dashboard;
    }

    /**
     * @inheritDoc
     */
    public function registerRoute(): string
    {
        return self::ROUTE;
    }

    /**
     * Only enable this endpoint if the user has access to the admin area and
     * the user has saved a user token, - ID and blog ID.
     */
    public function enabled(): bool
    {
        return $this->adminAccessAllowed();
    }

    /**
     * @inheritDoc
     */
    public function registerArguments(): array
    {
        return [
            'methods' => \WP_REST_Server::EDITABLE,
            'callback' => [$this, 'callback'],
            'permission_callback' => [$this->metricoolApi, 'hasAuthentication'],
        ];
    }

    /**
     * Log the user out and redirect to the dashboard
     */
    public function callback(\WP_REST_Request $request): \WP_REST_Response
    {
        $this->deleteAllOptions();

        return $this->sendHttpResponse(['success' => true]);
    }
}
