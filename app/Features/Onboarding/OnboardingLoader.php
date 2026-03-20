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
        return true;
    }

    /**
     * @inheritDoc
     */
    public function inScope(): bool
    {
        return (is_admin() && $this->userIsOnDashboard()) || $this->requestIsRestRequest();
    }
}
