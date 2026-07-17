<?php

declare(strict_types=1);

namespace Metricool\Http\Middleware;

use Metricool\Interfaces\MiddlewareInterface;
use Metricool\Traits\HasRestAccess;

/**
 * Checks if the current user has the 'metricool_manage' capability. If not, it returns a 403 Forbidden response.
 */
class HasMetricoolCapability implements MiddlewareInterface
{
    use HasRestAccess;

    /**
     * @param callable(\WP_REST_Request): mixed $next
     * @return mixed
     * @throws \Exception when not called with a capability
     */
    public function handle(\WP_REST_Request $request, callable $next)
    {
        if (!current_user_can('metricool_manage')) {
            return $this->sendHttpErrorResponse(
                __('You are not allowed to do this', 'metricool'),
                null,
                403
            );
        }

        return $next($request);
    }
}
