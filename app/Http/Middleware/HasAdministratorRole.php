<?php

declare(strict_types=1);

namespace Metricool\Http\Middleware;

use Metricool\Interfaces\MiddlewareInterface;
use Metricool\Traits\HasRestAccess;

/**
 * Checks if the current user is an administrator. If so, it returns a 403 Forbidden response.
 */
class HasAdministratorRole implements MiddlewareInterface
{
    use HasRestAccess;

    public function handle(\WP_REST_Request $request): ?\WP_REST_Response
    {
        if (!current_user_can('manage_options')) {
            return $this->sendHttpErrorResponse(
                __('You are not allowed to do this', 'metricool'),
                null,
                403
            );
        }

        return null;
    }
}
