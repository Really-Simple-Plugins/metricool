<?php

declare(strict_types=1);

namespace Metricool\Features\UserSettings;

use Metricool\Features\AbstractLoader;

class UserSettingsLoader extends AbstractLoader
{
    /**
     * @inheritDoc
     */
    public function isEnabled(): bool
    {
        return true; // todo?
    }

    /**
     * @inheritDoc
     */
    public function inScope(): bool
    {
        return true; // todo?
    }
}
