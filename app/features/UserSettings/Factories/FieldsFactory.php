<?php

namespace Metricool\Features\UserSettings\Factories;

use Adbar\Dot;
use Metricool\Helpers\Collection;
use Metricool\Features\UserSettings\Storage\AbstractStorage;
use Metricool\Features\UserSettings\Exceptions\StorageNotFoundException;

class FieldsFactory
{
    private Dot $userSettings;

    public function __construct(Dot $userSettings)
    {
        $this->userSettings = $userSettings;
    }

    /**
     * Method creates a Collection of Field classes with their respective
     * storage instances based on the configuration found in
     * config/user_settings.php.
     *
     * @throws StorageNotFoundException when a field's storage is not found
     * in the configuration.
     */
    public function createFromConfig(): Collection
    {
        $hydratedStorages = $this->hydrateConfigurationStorages();

        return $this->hydrateConfigurationFields(
            $hydratedStorages
        );
    }

    /**
     * Method hydrates plain text configuration storages from
     * config/user_settings.php into {@see AbstractStorage} instances based on
     * the configured 'storage' value.
     */
    private function hydrateConfigurationStorages(): Collection
    {
        $storages = [];
        foreach ($this->userSettings->get('storages', []) as $name => $config) {
            $storages[$name] = StorageFactory::createFromConfig($name, $config);
        }

        return new Collection($storages);
    }

    /**
     * Method hydrates plain text configuration fields from
     * config/user_settings.php into {@see Field} instances and binds their
     * respective storage instance.
     *
     * @throws StorageNotFoundException when a field's storage is not found
     * in the configuration returned from {@see hydrateConfigurationStorages}.
     */
    private function hydrateConfigurationFields(Collection $convertedStorages): Collection
    {
        $fields = [];
        foreach ($this->userSettings->get('fields', []) as $name => $config) {
            $field = FieldFactory::createFromConfig($name, $config);
            $storage = $convertedStorages->where('name', $field->getStorageName())->first();

            // Abort when storage not present in config
            if ($storage == null) {
                throw new StorageNotFoundException('Storage "' . $field->getStorageName() . '" not found for field: ' . $field->getName());
            }

            // Set validated storage to the field
            $field->setStorage($storage);

            // Add the field to the list
            $fields[$name] = $field;
        }

        return new Collection($fields);
    }

}