<?php

namespace Metricool\Controllers;

use Metricool\Bootstrap\Plugin;
use Metricool\Interfaces\ControllerInterface;
use Metricool\Services\DashboardService;
use Metricool\Services\MetricoolUserService;

class MetricoolUserController implements ControllerInterface
{
    private MetricoolUserService $service;
    private DashboardService $dashboard;

    public function __construct(MetricoolUserService $service, DashboardService $dashboard)
    {
        $this->service = $service;
        $this->dashboard = $dashboard;
    }

    public function register(): void
    {
        if (!$this->dashboard->isOnboardingCompleted()) {
            return;
        }

        add_filter('metricool_localize_dashboard_script', function ($script) {
            // Add premium status to settings
            $script['account']['is_premium'] = $this->service->update()->isPremium();

            return $script;
        });
    }
}