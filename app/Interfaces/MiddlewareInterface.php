<?php

declare(strict_types=1);

namespace Metricool\Interfaces;

interface MiddlewareInterface
{
    /**
     * Handle an incoming request. Return null to continue to the next
     * middleware/callback, or return a WP_REST_Response to short-circuit.
     */
    public function handle(\WP_REST_Request $request): ?\WP_REST_Response;
}
