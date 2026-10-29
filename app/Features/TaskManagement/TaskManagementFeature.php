<?php

declare(strict_types=1);

namespace Metricool\Features\TaskManagement;

use Metricool\Services\DashboardService;
use Metricool\Traits\HasAllowlistControl;
use Metricool\Interfaces\FeatureInterface;
use Metricool\Features\TaskManagement\Endpoints\TaskManagementEndpoints;

final class TaskManagementFeature implements FeatureInterface
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
     * Add the task management controller to the plugin controller registry.
     */
    public function registerControllers(array $existingControllers): array
    {
        return array_merge($existingControllers, [
            TaskManagementController::class,
        ]);
    }

    /**
     * Add the task management endpoint to the plugin endpoint registry.
     */
    public function registerEndpoints(array $existingEndpoints): array
    {
        return array_merge($existingEndpoints, [
            TaskManagementEndpoints::class,
        ]);
    }

    /**
     * Add the task management listener to the plugin listener registry.
     */
    public function registerListeners(array $existingListeners): array
    {
        return array_merge($existingListeners, [
            TaskManagementListener::class,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function isEnabled(): bool
    {
        return $this->dashboard->isOnboardingCompleted();
    }

    /**
     * Currently the only actions the {@see TaskManagementListener} has to
     * listen to are all triggered by admins. So this guard is broad enough.
     */
    public function inScope(): bool
    {
        return $this->userCanManage();
    }
}
