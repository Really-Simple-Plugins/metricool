<?php

namespace Metricool\Features\UserSettings;

use Metricool\App;
use Metricool\Features\UserSettings\Exceptions\StorageFailedException;
use Metricool\Features\UserSettings\Exceptions\StorageNotFoundException;
use Metricool\Features\UserSettings\Factories\FieldFactory;
use Metricool\Features\UserSettings\Factories\StorageFactory;
use Metricool\Features\UserSettings\Fields\Exceptions\ValidationErrors;
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
        $settings = [];

        // Find the necessary fields from the configuration
        $fields = $this->fields->whereIn('name', $fieldNames);

        // Group fields by storage to make it easier to retrieve all values with a single request
        $fieldsByStorage = $this->groupFieldsByStorage($fields);

        foreach ($fieldsByStorage as $storage => $fieldsToRetrieve) {
            // Retrieve all values from storage
            $values = $this->getStorage($storage)
                ->getMultiple(array_keys($fieldsToRetrieve));

            foreach ($fieldsToRetrieve as $field) {
                // Apply the retrieved values to the fields and add it to the results
                if (isset($values[$field->name])) {
                    $field->setValue($values[$field->name]);
                } else {
                    throw new StorageFailedException(
                        sprintf(
                            'Value could not be found for field: %s because it was not retrieved from storage: %s',
                            $field->name,
                            $storage
                        )
                    );
                }

                // Add the field to the results
                $settings[$field->name] = $field;
            }
        }

        return $settings;
    }

    /**
     * Validate and update settings and return their updated values
     * @return \WP_Error|array Returns WP_Error when validation failed, the validated data otherwise
     * @throws StorageFailedException when it fails to store data to it's storage
     */
    public function storeSettings(array $settings, \WP_REST_Request $request = null)
    {
        $validatedFields = $this->validateSettings($settings, $request);

        // If validation failed, return the errors
        if (is_wp_error($validatedFields)) {
            return $validatedFields;
        }

        // Group the validated fields by storage to store all values in a single request
        $fieldsByStorage = $this->groupFieldsByStorage($validatedFields);

        // Keep track of the stored settings
        $storedSettings = [];

        // Attempt to save fields to storages
        try {
            foreach ($fieldsByStorage as $storageName => $fieldsToStore) {
                $settingsToStore = $this->getFieldValues($fieldsToStore);
                $this->getStorage($storageName)->setMultiple($settingsToStore);

                // Push updated settings to the stored Settings
                $storedSettings[] = $settingsToStore;
            }
        } catch (\Exception $e) {
            throw new StorageFailedException($e->getMessage());
        }

        // Return the stored settings
        return $storedSettings;
    }

    /**
     * Validate the fields and return their validated values.
     * @return \WP_Error|Field[] Returns WP_Error when validation failed,
     * otherwise and array of validated Fields
     */
    private function validateSettings(array $settings, \WP_REST_Request $request = null)
    {
        $validatedFields = [];
        $errorContainer = new \WP_Error();

        foreach ($settings as $fieldName => $value) {
            $field = $this->getField($fieldName);

            if ($field === null) {
                // Continue to the next field if the field is unknown
                continue;
            }

            // Validate the field value
            try {
                $field->validate($value, $request);
            } catch (ValidationErrors $e) {
                foreach ($e->validationErrors as $error) {
                    $errorContainer->add($fieldName, $error->getMessage(), ['field' => $fieldName]);
                }

                // Continue to the next field when validation failed
                continue;
            }

            // Set the validated field value
            $field->setValue($value);

            // Add the field to the validated data
            $validatedFields[$fieldName] = $field;
        }

        return $errorContainer->has_errors() ? $errorContainer : $validatedFields;
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
     * Initialize fields from user_settings "fields" configuration
     * @throws StorageNotFoundException when a field's storage is not present in the storages configuration
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
    private function groupFieldsByStorage(array $fields): array
    {
        $fieldsByStorage = [];
        foreach ($fields as $field) {
            $storageName = $field->getStorageName();
            if (!isset($fieldsByStorage[$storageName])) {
                $fieldsByStorage[$storageName] = [];
            }
            $fieldsByStorage[$storageName][$field->getName()] = $field;
        }
        return $fieldsByStorage;
    }

    /**
     * Create an array with the values of the fields
     */
    protected function getFieldValues(array $fields): array
    {
        return array_map(function (Field $field) {
            return $field->getValue();
        }, $fields);
    }
}