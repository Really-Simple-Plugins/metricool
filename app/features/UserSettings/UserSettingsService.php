<?php

namespace Metricool\Features\UserSettings;

use Metricool\App;
use Metricool\Features\UserSettings\Fields\Field;
use Metricool\Features\UserSettings\Storage\AbstractStorage;
use Metricool\Features\UserSettings\Storage\ApiStorage;
use Metricool\Features\UserSettings\Storage\DatabaseStorage;
use Metricool\Features\UserSettings\Storage\Exceptions\StorageClientRequiredException;
use Metricool\Helpers\Collection;

// todo: add support for custom storages
// todo: add support for custom fields

/**
 * This service is responsible for storing and retrieving user settings.
 */
class UserSettingsService
{
    /** @var Collection|Field[] */
    private Collection $fields;
    /** @var Collection|AbstractStorage[] */
    private $storages = [];

    public function __construct()
    {
        $config = App::userSettings();

        $this->initializeStorages($config['storages'] ?? []);
        $this->initializeFields($config['fields'] ?? []);
    }

    public function getField(string $fieldName): ?Field
    {
        return $this->fields->where('name', $fieldName)->first();
    }

    public function getStorage(string $storageName): ?AbstractStorage
    {
        return $this->storages->where('name', $storageName)->first();
    }

    public function hasStorage(string $storageName): bool
    {
        return $this->getStorage($storageName) !== null;
    }

    /**
     * @throws \Exception
     */
    public function getAllSettings(): array
    {
        $fieldNames = $this->fields->keys();

        return $this->getSettings($fieldNames);
    }

    /**
     * @throws \Exception
     */
    public function getSettingsForSection(string $section): array
    {
        $fieldNames = $this->fields->where('section', $section)->keys();

        return $this->getSettings($fieldNames);
    }

    /**
     * @throws \Exception
     */
    public function getSetting($fieldName)
    {
        return $this->getSettings([$fieldName]);
    }

    /**
     * @throws \Exception
     */
    public function getSettings(array $fieldNames): array
    {
        $results = [];

        // Group fields by storage to make it easier to retrieve all values from a single storage
        $fieldsByStorage = $this->groupFieldsByStorage($fieldNames);

        foreach ($fieldsByStorage as $storageName => $fields) {
            $storageFieldNames = array_keys($fields);
            $storage = $this->getStorage($storageName);

            // Retrieve all values from storage
            $values = $storage->getMultiple($storageFieldNames);

            // Apply the retrieved value to the fields and add it to the results
            foreach ($fields as $field) {
                $field->setValue($values[$field->name] ?? $field->getDefaultValue());
                $results[$field->name] = $field->getValue();
            }
        }

        return $results;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getSettingValue($fieldName)
    {
        return $this->getSettings([$fieldName]);
    }

    public function updateSettings(array $data, $request = null)
    {
        $validatedFields = $this->validateFields($data, $request);

        // If validation failed, return errors
        if (is_wp_error($validatedFields)) {
            return $validatedFields;
        }

        $dataByStorage = [];
        foreach ($validatedFields as $fieldName => $field) {
            $storage = $this->getStorage($field->getStorage());
            // Group fields by storage to make it easier to store all values of a storage in a single request
            if (!isset($dataByStorage[$storage->name])) {
                $dataByStorage[$storage->name] = [];
            }
            // Add field value to the storage data
            $dataByStorage[$storage->name][$fieldName] = $field->getValue();
        }

        // Save fields to storages
        foreach ($dataByStorage as $storageName => $storageData) {
            $storage = $this->getStorage($storageName);

            // Attempt to store data to storage
            $storage->setMultiple($storageData);
        }

        return array_map(function ($field) {
            return $field->getValue();
        }, $validatedFields);
    }

    /*
     * @return \WP_Error|Field[]
     */
    private function validateFields(array $data)
    {
        $errors = new \WP_Error();
        $validatedData = [];

        foreach ($data as $fieldName => $value) {
            $field = $this->getField($fieldName);

            if ($field === null) {
                // Continue to the next field if the field is unknown
                continue;
            }

            // Validate the field value
            $fieldErrors = $field->validate($value);

            if (!empty($fieldErrors)) {
                foreach ($fieldErrors as $error) {
                    $errors->add($fieldName, $error, ['field' => $fieldName]);
                }
                continue;
            }

            // Set the validated field value
            $field->setValue($value);

            // Add the new field value to the validated data
            $validatedData[$fieldName] = $field;
        }

        if ($errors->has_errors()) {
            return $errors;
        }

        return $validatedData;
    }

    private function initializeFields(array $fieldsConfig): void
    {
        $fields = [];
        foreach ($fieldsConfig as $name => $config) {
            $field = new Field($name, $config);

            // Continue to the next field if the storage could not be found
            if (!$this->hasStorage($field->getStorage())) {
                throw new StorageClientRequiredException('Storage "' . $field->getStorage() . '" not found for field: ' . $field->getName());
            }

            $fields[$name] = $field;
        }

        $this->fields = new Collection($fields);
    }

    private function initializeStorages(array $storagesConfig): void
    {
        $storages = [];
        foreach ($storagesConfig as $name => $config) {
            $storages[$name] = $this->createStorage($name, $config);
        }

        $this->storages = new Collection($storages);
    }

    private function createStorage(string $name, array $config): AbstractStorage
    {
        switch ($config['type'] ?? 'database') {
            case 'api':
                return new ApiStorage($name, $config);
            case 'database':
            default:
                return new DatabaseStorage($name, $config);
        }
    }

    /**
     * @return array<string, Field[]>
     */
    private function groupFieldsByStorage(array $fieldNames): array
    {
        $fieldsByStorage = [];
        foreach ($fieldNames as $fieldName) {
            $field = $this->getField($fieldName);
            $storageName = $field->getStorage();
            if (!isset($fieldsByStorage[$storageName])) {
                $fieldsByStorage[$storageName] = [];
            }
            $fieldsByStorage[$storageName][$fieldName] = $field;
        }
        return $fieldsByStorage;
    }
}