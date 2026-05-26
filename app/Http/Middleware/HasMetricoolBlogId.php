<?php

declare(strict_types=1);

namespace Metricool\Http\Middleware;

use Metricool\Http\Metricool\MetricoolApi;
use Metricool\Interfaces\MiddlewareInterface;
use Metricool\Traits\HasRestAccess;

/**
 * Checks if a brand / blog id is set. This is required for some Metricool API endpoints
 */
class HasMetricoolBlogId implements MiddlewareInterface
{
    use HasRestAccess;

    private MetricoolApi $metricool;

    public function __construct(MetricoolApi $metricool)
    {
        $this->metricool = $metricool;
    }

    public function handle(\WP_REST_Request $request): ?\WP_REST_Response
    {
        if (!$this->metricool->hasBlogId()) {
            return $this->sendHttpErrorResponse(
                'Brand / blog id missing',
                null,
                403
            );
        }

        return null;
    }
}
