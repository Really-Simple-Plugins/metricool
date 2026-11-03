<?php

declare(strict_types=1);

namespace Metricool\Features\Onboarding;

use Metricool\Services\DashboardService;
use Metricool\Traits\HasAllowlistControl;
use Metricool\Interfaces\FeatureInterface;
use Metricool\Features\Onboarding\Endpoints\OAuthRedirectEndpoint;
use Metricool\Features\Onboarding\Endpoints\CreateAccountEndpoint;
use Metricool\Features\Onboarding\Endpoints\FinishOnboardingEndpoint;

final class OnboardingFeature implements FeatureInterface
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
    }

    /**
     * Add the onboarding controller to the plugin controller registry.
     */
    public function registerControllers(array $existingControllers): array
    {
        return array_merge($existingControllers, [
            OnboardingController::class,
        ]);
    }

    /**
     * Add the onboarding endpoints to the plugin endpoint registry.
     */
    public function registerEndpoints(array $existingEndpoints): array
    {
        return array_merge($existingEndpoints, [
            CreateAccountEndpoint::class,
            FinishOnboardingEndpoint::class,
            OAuthRedirectEndpoint::class,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function isEnabled(): bool
    {
        return $this->dashboard->isOnboardingCompleted() === false;
    }

    /**
     * @inheritDoc
     */
    public function inScope(): bool
    {
        return $this->userCanManage();
    }
}
