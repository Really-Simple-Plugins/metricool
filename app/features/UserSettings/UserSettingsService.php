<?php

namespace Metricool\Features\UserSettings;

use Metricool\App;
use Metricool\Features\UserSettings\Exceptions\StorageFailedException;
use Metricool\Features\UserSettings\Exceptions\StorageNotFoundException;
use Metricool\Features\UserSettings\Factories\FieldFactory;
use Metricool\Features\UserSettings\Factories\StorageFactory;
use Metricool\Features\UserSettings\Fields\Exceptions\FieldValidateExceptions;
use Metricool\Features\UserSettings\Fields\Field;
use Metricool\Features\UserSettings\Storage\AbstractStorage;
use Metricool\Helpers\Collection;

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

        //todo: This should probably be moved to someplace else
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
     * Return an array with setting names and values of the fieldNames
     * @throws \Exception
     */
    public function getSettings(array $fieldNames): array
    {
        $results = [];

        // Get fields by storage to make it easier to retrieve all values with a single request
        $fieldsByStorage = $this->groupFieldsByStorage($fieldNames);

        foreach ($fieldsByStorage as $storageName => $fields) {
            $storage = $this->getStorage($storageName);

            // Retrieve all values from storage
            $values = $storage->getMultiple(array_keys($fields));

            // Apply the retrieved value to the fields and add it to the results
            foreach ($fields as $field) {
                if (isset($values[$field->name])) {
                    $field->setValue($values[$field->name]);
                }

                $results[$field->name] = $field->getValue();
            }
        }

        return $results;
    }

    /**
     * Validate and update settings and return their updated values
     * @return \WP_Error|Field[] Returns WP_Error when validation failed
     * @throws \Exception
     */
    public function updateSettings(array $data, $request = null)
    {
        $validatedFields = $this->validateFields($data, $request);

        // If validation failed, return the errors
        if (is_wp_error($validatedFields)) {
            return $validatedFields;
        }

        // Group the validated fields by storage to store all values in a single request
        $dataByStorage = [];
        foreach ($validatedFields as $fieldName => $field) {
            $storageName = $field->getStorageName();

            if (!isset($dataByStorage[$storage->name])) {
                $dataByStorage[$storageName] = [];
            }

            $dataByStorage[$storageName][$fieldName] = $field->getValue();
        }

        // Attempt to save fields to storages
        try {
            foreach ($dataByStorage as $storageName => $storageData) {
                $this->getStorage($storageName)->setMultiple($storageData);
            }
        } catch (\Exception $e) {
            throw new StorageFailedException($e->getMessage());
        }

        // Return the validated field keys and their values
        return array_map(function ($field) {
            return $field->getValue();
        }, $validatedFields);
    }

    /**
     * Validate the fields and return their validated values.
     * @return \WP_Error|Field[] Returns WP_Error when validation failed,
     * otherwise and array of validated Fields
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
            try {
                $field->validate($value, $request);
            } catch (FieldValidateExceptions $e) {
                foreach ($e->validationErrors as $error) {
                    $errors->add($fieldName, $error->getMessage(), ['field' => $fieldName]);
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
            $storages[$name] = StorageFactory::create($name, $config);
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
            $field = FieldFactory::create($name, $config);

            $storage = $this->getStorage($field->getStorageName());

            // Abort when storage not present in config
            if ($storage == null) {
                throw new StorageNotFoundException('Storage "' . $field->getStorageName() . '" not found for field: ' . $field->getName());
            }

            $fields[$name] = $field;
        }

        $this->fields = new Collection($fields);
    }

    /**
     * Creates an associative array with the fields grouped by storage.
     */
    private function groupFieldsByStorage(array $fieldNames): array
    {
        $fieldsByStorage = [];
        foreach ($fieldNames as $fieldName) {
            $field = $this->getField($fieldName);
            $storageName = $field->getStorageName();

            if (!isset($fieldsByStorage[$storageName])) {
                $fieldsByStorage[$storageName] = [];
            }

            $fieldsByStorage[$storageName][$fieldName] = $field;
        }
        return $fieldsByStorage;
    }
}