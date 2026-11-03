<?php

declare(strict_types=1);

namespace Metricool\Interfaces;

interface SingleEndpointInterface
{
    /**
     * The route name to register. Will be used as the array key for routes
     * array in: {@see EndpointManager::registerWordPressRestRoutes}
     */
    public function registerRoute(): string;

    /**
     * Arguments you can use are documented here: {@see register_rest_route}
     */
    public function registerArguments(): array;

    /**
     * This method should return true if the endpoint is enabled, false
     * otherwise. Endpoint will not be registered if this method returns false:
     * {@see EndpointManager::registerEndpoints}
     */
    public function enabled(): bool;
}
