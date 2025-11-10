<?php

namespace Metricool\Features\UserSettings;

use Metricool\App;
use Metricool\Features\UserSettings\Exceptions\StorageNotFoundException;
use Metricool\Features\UserSettings\Fields\Field;
use Metricool\Features\UserSettings\Storage\AbstractStorage;
use Metricool\Features\UserSettings\Storage\ApiStorage;
use Metricool\Features\UserSettings\Storage\DatabaseStorage;
use Metricool\Helpers\Collection;

// todo: add support for custom storages
// todo: add support for custom fields?

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

    /**
     * Returns the field instance
     */
    public function getField(string $fieldName): ?Field
    {
        return $this->fields->where('name', $fieldName)->first();
    }

    /**
     * Returns the storage instance
     */
    public function getStorage(string $storageName): ?AbstractStorage
    {
        return $this->storages->where('name', $storageName)->first();
    }

    /**
     * Return an array with keys and values of all the settings
     * @throws \Exception
     */
    public function getAllSettings(): array
    {
        $fieldNames = $this->fields->keys();

        return $this->getSettings($fieldNames);
    }

    /**
     * Return an array with keys and values of all the settings for this section
     * @throws \Exception
     */
    public function getSettingsForSection(string $section): array
    {
        $fieldNames = $this->fields->where('section', $section)->keys();

        return $this->getSettings($fieldNames);
    }

    /**
     * Return an array with keys and values of the fieldNames
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
     * Validate and update settings from data and return their updated values
     * @return \WP_Error|Field[] Returns WP_Error when validation failed
     */
    public function updateSettings(array $data, $request = null)
    {
        $validatedFields = $this->validateFields($data, $request);

        // If validation failed, return errors
        if (is_wp_error($validatedFields)) {
            return $validatedFields;
        }

        // Group fields by storage to make it easier to store all values of a storage in a single request
        $dataByStorage = [];
        foreach ($validatedFields as $fieldName => $field) {
            $storage = $this->getStorage($field->getStorage());

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

        // Return the validated field values
        return array_map(function ($field) {
            return $field->getValue();
        }, $validatedFields);
    }

    /**
     * Validate the fields and return their validated values.
     * @return \WP_Error|Field[] Returns WP_Error when validation failed
     */
    private function validateFields(array $data, \WP_REST_Request $request)
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

    /**
     * Initialize storages from user_settings configuration
     */
    private function initializeStorages(array $storagesConfig): void
    {
        $storages = [];
        foreach ($storagesConfig as $name => $config) {
            $storages[$name] = $this->createStorage($name, $config);
        }

        $this->storages = new Collection($storages);
    }

    /**
     * Initialize fields from user_settings configuration
     */
    private function initializeFields(array $fieldsConfig): void
    {
        $fields = [];
        foreach ($fieldsConfig as $name => $config) {
            $field = new Field($name, $config);
            $storage = $this->getStorage($field->getStorage());

            // Abort when storage not present in config
            if ($storage == null) {
                throw new StorageNotFoundException('Storage "' . $field->getStorage() . '" not found for field: ' . $field->getName());
            }

            $fields[$name] = $field;
        }

        $this->fields = new Collection($fields);
    }

    /**
     * Creates a storage instance from config
     */
    private function createStorage(string $name, array $config): AbstractStorage
    {
        switch ($config['type']) {
            case 'api':
                return new ApiStorage($name, $config);
            case 'database':
            default:
                return new DatabaseStorage($name, $config);
        }
    }

    /**
     * Creates an associative array with the field names grouped by storage.
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