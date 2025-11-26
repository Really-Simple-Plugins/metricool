<?php

declare(strict_types=1);

namespace Metricool\Features\UserSettings;

use Metricool\Bootstrap\App;
use Metricool\Support\Helpers\Collection;
use Metricool\Interfaces\FeatureInterface;
use Metricool\Features\UserSettings\Factories\FieldsFactory;
use Metricool\Features\UserSettings\Exceptions\StorageNotFoundException;

class UserSettingsController implements FeatureInterface
{
    private UserSettingsEndpoint $endpoints;

    /**
     * @throws \Exception
     */
    public function __construct()
    {
        $fields = $this->getFieldsFromConfig();
        $this->endpoints = new UserSettingsEndpoint(
            new UserSettingsService($fields)
        );
    }

    public function register(): void
    {
        $this->endpoints->register();
    }

    /**
 * Retrieve the fields from the configuration file and convert them into
     * Field instances.
     * @throws StorageNotFoundException When the config file is not correctly
     * set up.
     */
    private function getFieldsFromConfig(): Collection
    {
        $factory = new FieldsFactory(
            App::getInstance()->settings->all(),
        );
        return $factory->createFromConfig();
    }
}
