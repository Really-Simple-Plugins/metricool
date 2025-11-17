<?php

namespace Metricool\Features\UserSettings;

use Metricool\App;
use Metricool\Helpers\Collection;
use Metricool\Interfaces\FeatureInterface;
use Metricool\Features\UserSettings\Factories\FieldFactory;
use Metricool\Features\UserSettings\Factories\StorageFactory;
use Metricool\Features\UserSettings\Exceptions\StorageNotFoundException;

class UserSettingsController implements FeatureInterface
{
    private UserSettingsEndpoint $endpoints;

    /**
     * @throws \Exception
     */
    public function __construct()
    {
        $convertedStorages = $this->convertedUserSettingStorages();
        $convertedFields = $this->convertedUserSettingFields($convertedStorages);

        $this->endpoints = new UserSettingsEndpoint(
            new UserSettingsService($convertedStorages, $convertedFields)
        );
    }

    public function register()
    {
        $this->endpoints->register();
    }

    /**
     * Initialize classes for storages from user_settings configuration and
     * return it as a Collection.
     */
    private function convertedUserSettingStorages(): Collection
    {
        $storages = [];
        foreach (App::userSettings('storages', []) as $name => $config) {
            $storages[$name] = StorageFactory::createFromConfig($name, $config);
        }

        return new Collection($storages);
    }

    /**
     * Initialize fields from user_settings "fields" configuration
     * @throws StorageNotFoundException when a field's storage is not present in the storages configuration
     */
    private function convertedUserSettingFields(Collection $convertedStorages): Collection
    {
        $fields = [];
        foreach (App::userSettings('fields', []) as $name => $config) {
            $field = FieldFactory::createFromConfig($name, $config);
            $storage = $convertedStorages->where('name', $field->getStorage())->first();

            // Abort when storage not present in config
            if ($storage == null) {
                throw new StorageNotFoundException('Storage "' . $field->getStorage() . '" not found for field: ' . $field->getName());
            }

            // Add the field to the list
            $fields[$name] = $field;
        }

        return new Collection($fields);
    }
}