<?php

declare(strict_types=1);

namespace Metricool\Features\AdminNotices;

use Metricool\Traits\HasAllowlistControl;
use Metricool\Interfaces\FeatureInterface;
use Metricool\Features\AdminNotices\Endpoints\AdminNoticesEndpoint;

final class AdminNoticesFeature implements FeatureInterface
{
    use HasAllowlistControl;

    /**
     * @inheritDoc
     */
    public function boot(): void
    {
        add_filter('metricool_plugin_controllers', [$this, 'registerControllers']);
        add_filter('metricool_plugin_endpoints', [$this, 'registerEndpoints']);
    }

    /**
     * Add the admin notices controller to the plugin controller registry.
     */
    public function registerControllers(array $existingControllers): array
    {
        return array_merge($existingControllers, [
            AdminNoticesController::class,
        ]);
    }

    /**
     * Add the admin notices endpoint to the plugin endpoint registry.
     */
    public function registerEndpoints(array $existingEndpoints): array
    {
        return array_merge($existingEndpoints, [
            AdminNoticesEndpoint::class,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function isEnabled(): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function inScope(): bool
    {
        return $this->userCanManage();
    }
}
