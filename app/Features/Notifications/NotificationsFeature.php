<?php

declare(strict_types=1);

namespace Metricool\Features\Notifications;

use Metricool\Features\Notifications\Endpoints\NotificationsEndpoint;
use Metricool\Interfaces\FeatureInterface;
use Metricool\Services\DashboardService;
use Metricool\Traits\HasAllowlistControl;

final class NotificationsFeature implements FeatureInterface
{
    use HasAllowlistControl;

    private DashboardService $dashboard;

    public function __construct(DashboardService $dashboard)
    {
        $this->dashboard = $dashboard;
    }

    /**
     * @inheritDoc
     */
    public function boot(): void
    {
        add_filter('metricool_plugin_controllers', [$this, 'registerControllers']);
        add_filter('metricool_plugin_endpoints', [$this, 'registerEndpoints']);
        add_filter('metricool_plugin_listeners', [$this, 'registerListeners']);
    }

    /**
     * Add the notifications controller to the plugin controller registry.
     */
    public function registerControllers(array $existingControllers): array
    {
        return array_merge($existingControllers, [NotificationsController::class]);
    }

    /**
     * Add the notifications endpoint to the plugin endpoint registry.
     */
    public function registerEndpoints(array $existingEndpoints): array
    {
        return array_merge($existingEndpoints, [NotificationsEndpoint::class]);
    }

    /**
     * Add the notifications listener to the plugin listener registry.
     */
    public function registerListeners(array $existingListeners): array
    {
        return array_merge($existingListeners, [NotificationListener::class]);
    }

    /**
     * @inheritDoc
     */
    public function isEnabled(): bool
    {
        return $this->dashboard->isOnboardingCompleted();
    }

    /**
     * @inheritDoc
     */
    public function inScope(): bool
    {
        return $this->userCanManage();
    }
}
