<?php

declare(strict_types=1);

namespace Metricool\Features\UserSettings;

use Metricool\Features\AbstractLoader;
use Metricool\Support\Helpers\Storages\EnvironmentConfig;
use Metricool\Support\Helpers\Storages\RequestStorage;

class UserSettingsLoader extends AbstractLoader
{
    public function __construct(EnvironmentConfig $env, RequestStorage $request)
    {
        parent::__construct($env, $request);
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
        return current_user_can('metricool_manage');
    }
}
