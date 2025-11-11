<?php

namespace Metricool\Features\UserSettings;

use Metricool\Interfaces\FeatureInterface;

class UserSettingsController implements FeatureInterface
{
    private UserSettingsService $service;
    private UserSettingsEndpoint $endpoints;

    /**
     * @throws \Exception
     */
    public function __construct()
    {
        $this->service = new UserSettingsService();
        $this->endpoints = new UserSettingsEndpoint($this->service);
    }

    public function register()
    {
        $this->endpoints->register();
    }
}