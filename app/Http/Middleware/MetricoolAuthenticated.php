<?php

declare(strict_types=1);

namespace Metricool\Http\Middleware;

use Metricool\Http\Metricool\MetricoolClient;
use Metricool\Traits\HasRestAccess;

class MetricoolAuthenticated implements MiddlewareInterface
{
    use HasRestAccess;

    private MetricoolClient $client;

    public function __construct(MetricoolClient $client)
    {
        $this->client = $client;
    }

    public function handle(\WP_REST_Request $request): ?\WP_REST_Response
    {
        if (!$this->client->hasAuthentication()) {
            return $this->sendHttpErrorResponse(
                __('Unauthorized. Please log in to Metricool.', 'metricool'),
                null,
                401
            );
        }

        return null;
    }
}
