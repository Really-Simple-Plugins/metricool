<?php

namespace Metricool\Features\UserSettings;

use Metricool\App;
use Metricool\Features\UserSettings\Fields\Field;
use Metricool\Features\UserSettings\Storage\AbstractStorage;
use Metricool\Features\UserSettings\Storage\ApiStorage;
use Metricool\Features\UserSettings\Storage\DatabaseStorage;
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

    public function getAllSettings(): array
    {
        $fieldNames = $this->fields->keys();
        return $this->getSettings($fieldNames);
    }

    public function getSettingsForSections(string $section): array
    {
        $fieldNames = $this->fields->where('section', $section)->keys();
        return $this->getSettings($fieldNames);
    }

    /**
     * @throws \Exception
     */
    public function getSettings(array $fieldNames): array
    {
        $settings = [];

        // group fields by storage to make it easier to retrieve all values from a single storage
        $fieldsByStorage = $this->groupFieldsByStorage($fieldNames);

        foreach ($fieldsByStorage as $storageName => $fieldNames) {
            $storage = $this->getStorage($storageName);
            $values = $storage->getMultiple($fieldNames);

            foreach ($fieldNames as $fieldName) {
                $field = $this->getField($fieldName);
                $field->setValue($values[$fieldName] ?? $field->getDefaultValue());

                $settings[$fieldName] = [
                    'value' => $field->getValue(),
                    'type' => $field->getType(),
                    'section' => $field->getSection(),
                ];
            }
        }

        return $settings;
    }

    /**
     * @throws \Exception
     */
    public function updateSettings(array $data, $request = null)
    {
        $errors = new \WP_Error();
        $validatedData = [];
        $dataByStorage = [];

        foreach ($data as $fieldName => $value) {
            $field = $this->getField($fieldName);

            if ($field === null) {
                // continue to the next field if the field is unknown
                continue;
            }

            $field->setValue($value);

            // Validate the field value
            $fieldErrors = $field->validate($value, $request);

            if (!empty($fieldErrors)) {
                foreach ($fieldErrors as $error) {
                    $errors->add('validation_failed', $error, $fieldName);
                }
                // continue to the next field if the field validation failed
                continue;
            }

            // Add the new field value to the validated data
            $validatedData[$fieldName] = $field->getValue();

            // Group fields by storage to make it easier to store all values of a storage in a single request
            $storageName = $field->getStorage();
            if (!isset($dataByStorage[$storageName])) {
                $dataByStorage[$storageName] = [];
            }
            // Add the field value to the storage data
            $storage = $this->getStorage($storageName);
            $dataByStorage[$storageName][$fieldName] = $storage->sanitizeValue($value, $field->getType());
        }

        // Save fields to storages
        foreach ($dataByStorage as $storageName => $storageData) {
            $storage = $this->getStorage($storageName);
            if ($storage === null) {
                $errors->add('unknown_storage', 'Storage not found', ['storage' => $storageName]);
                // continue to the next storage if storage is unknown
                continue;
            }

            // Attempt to store data to storage
            try {
                $storage->setMultiple($storageData);
            } catch (\Exception $e) {
                $errors->add('storage_failed', 'Failed to save to storage', ['storage' => $storageName]);
            }
        }

        // If validation failed, return errors
        if ($errors->has_errors()) {
            return $errors;
        }

        return $validatedData;
    }

    /**
     * @throws \Exception
     */
    public function getField(string $fieldName): ?Field
    {
        $field = $this->fields->where('name', $fieldName)->first();

        if (!$field) {
            throw new \Exception("Field {$fieldName} not found in fields");
        }

        return $field;
    }

    /**
     * @throws \Exception
     */
    public function getStorage(string $storageName): ?AbstractStorage
    {
        $storage = $this->storages->where('name', $storageName)->first();
        if (!$storage) {
            throw new \Exception("Storage {$storageName} not found in storages");
        }

        return $storage;
    }


    private function initializeFields(array $fieldsConfig): void
    {
        $fields = [];
        foreach ($fieldsConfig as $name => $config) {
            $fields[$name] = new Field($name, $config);
        }

        $this->fields = new Collection($fields);
    }

    private function initializeStorages(array $storagesConfig): void
    {
        $storages = [];
        foreach ($storagesConfig as $name => $config) {
            $storages[$name] = $this->createStorage($name, $config);
        }

        // Add default database storage if not defined
        if (!isset($this->storages['database'])) {
            $storages['database'] = new DatabaseStorage('database', ['prefix' => '']);
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

    private function groupFieldsByStorage(array $fieldNames): array
    {
        $fieldsByStorage = [];
        foreach ($fieldNames as $fieldName) {
            $field = $this->getField($fieldName);
            $storageName = $field->getStorage();
            if (!isset($fieldsByStorage[$storageName])) {
                $fieldsByStorage[$storageName] = [];
            }
            $fieldsByStorage[$storageName][] = $field->getName();
        }
        return $fieldsByStorage;
    }
}