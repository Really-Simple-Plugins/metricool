<?php

declare(strict_types=1);

namespace Metricool\Features\UserSettings;

use Metricool\Features\AbstractLoader;
use Metricool\Services\DashboardService;
use Metricool\Support\Helpers\Storages\EnvironmentConfig;
use Metricool\Support\Helpers\Storages\RequestStorage;

class UserSettingsLoader extends AbstractLoader
{
    private DashboardService $dashboard;

    public function __construct(EnvironmentConfig $env, RequestStorage $request, DashboardService $dashboard)
    {
        parent::__construct($env, $request);

        $this->dashboard = $dashboard;
    }

    /**
     * @inheritDoc
     */
    public function isEnabled(): bool
    {
        return $this->dashboard->isOnboardingCompleted() && current_user_can('metricool_manage');
    }

    /**
     * @inheritDoc
     */
    public function inScope(): bool
    {
        return true;
    }
}
