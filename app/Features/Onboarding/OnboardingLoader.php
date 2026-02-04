<?php

declare(strict_types=1);

namespace Metricool\Features\Onboarding;

use Metricool\Features\AbstractLoader;

class OnboardingLoader extends AbstractLoader
{
    /**
     * @inheritDoc
     */
    public function isEnabled(): bool
    {
        return (bool) get_option('metricool_onboarding_completed', false) === false;
    }

    /**
     * @inheritDoc
     */
    public function inScope(): bool
    {
        return (is_admin() && $this->userIsOnDashboard()) || $this->requestIsRestRequest();
    }
}
