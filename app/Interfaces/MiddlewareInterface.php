<?php

declare(strict_types=1);

namespace Metricool\Interfaces;

interface MiddlewareInterface
{
    /**
     * Handle an incoming request. Call and return $next($request) to continue
     * to the next middleware/callback, or return a WP_REST_Response to
     * short-circuit.
     *
     * @param callable(\WP_REST_Request): mixed $next
     * @return mixed
     */
    public function handle(\WP_REST_Request $request, callable $next);
}
