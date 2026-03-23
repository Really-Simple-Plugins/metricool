<?php

declare(strict_types=1);

namespace Metricool\Features\Onboarding;

use Metricool\Features\AbstractLoader;
use Metricool\Services\DashboardService;
use Metricool\Support\Helpers\Storages\EnvironmentConfig;
use Metricool\Support\Helpers\Storages\RequestStorage;

class OnboardingLoader extends AbstractLoader
{
    private DashboardService $dashboard;

    public function __construct(EnvironmentConfig $env, RequestStorage $request, DashboardService $dashboard)
    {
        $this->dashboard = $dashboard;
        parent::__construct($env, $request);
    }
    /**
     * @inheritDoc
     */
    public function isEnabled(): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function inScope(): bool
    {
        return false;//(is_admin() && $this->userIsOnDashboard()) || $this->requestIsRestRequest();
    }
}
