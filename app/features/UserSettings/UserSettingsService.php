<?php

namespace Metricool\Features\UserSettings;

use Metricool\App;
use Metricool\Features\UserSettings\Exceptions\StorageFailedException;
use Metricool\Features\UserSettings\Exceptions\StorageNotFoundException;
use Metricool\Features\UserSettings\Exceptions\ValidationFailedExceptions;
use Metricool\Features\UserSettings\Factories\FieldFactory;
use Metricool\Features\UserSettings\Factories\StorageFactory;
use Metricool\Features\UserSettings\Fields\Field;
use Metricool\Features\UserSettings\Storage\AbstractStorage;
use Metricool\Features\UserSettings\Validators\Exceptions\ValidatorFailedException;
use Metricool\Helpers\Collection;

/**
 * This service is responsible for storing and retrieving user settings.
 */
class UserSettingsService
{
    /**
     * This property holds all the available fields
     * @var Collection|Field[]
     */
    public Collection $fields;

    /**
     * This property holds all the available storages
     * @var Collection|AbstractStorage[]
     */
    private $storages;

    public function __construct(Collection $storages, Collection $fields)
    {
        $this->storages = $storages;
        $this->fields = $fields;
    }

    /**
     * Return an array with keys and values of all the settings, optionally
     * filtered by section.
     * @throws \Exception
     */
    public function getSettings(?string $section = null): array
    {
        $fields = $this->fields;

        if (!empty($section)) {
            $fields = $fields->where('section', $section);
        }

        return $this->getFieldSettings($fields->keys());
    }

    /**
     * Return an array of fields and their values
     * @return Field[]
     * @throws \Exception when data retrieval fails
     * @throws StorageFailedException when data was not present in storage
     */
    private function getFieldSettings(array $fieldNames): array
    {
        // Find the necessary fields from the configuration
        $fields = $this->fields->whereIn('name', $fieldNames);
        // Group fields by storage to make it easier to retrieve all values with a single request
        $fieldsByStorage = $this->groupFieldsByStorage($fields);

        foreach ($fieldsByStorage as $storage => $fieldsToRetrieve) {
            // Get an array of storage names of the field
            $fieldStorageNames = $fieldsToRetrieve->map(function (Field $field) {
                return $field->getNameForStorage();
            })->toArray();

            // Retrieve the data from storage
            // todo - getStorage can return null, but it is already validated prior to this point
            // todo - we should add it to the Field object in \Metricool\Features\UserSettings\UserSettingsController::convertedUserSettingFields
            $storageData = $this->getStorage($storage)
                ->getMultiple($fieldStorageNames);

            foreach ($fieldsToRetrieve as $field) {
                $fieldStorageName = $field->getNameForStorage();

                // Throw Exception when field was not found in storage
                if (!array_key_exists($fieldStorageName, $storageData)) {
                    throw new StorageFailedException(
                        sprintf(
                            'Value could not be found for field: %s because "%s" was not retrieved from storage: %s',
                            $field->getName(),
                            $fieldStorageName,
                            $storage
                        )
                    );
                }

                // Apply the retrieved value to the field
                $field->setValue($storageData[$fieldStorageName]);
            }
        }

        return $this->convertFieldsToSettings($fields);
    }

    /**
     * Validate and update settings and return their updated values
     * @throws ValidationFailedExceptions with all the validation errors when validation fails
     * @throws StorageFailedException when it fails to store data to it's storage
     */
    public function storeSettings(array $settings, \WP_REST_Request $request): array
    {
        $validatedFields = $this->validateSettings($settings, $request);
        // Group the validated fields by storage to store all values in a single request
        $fieldsByStorage = $this->groupFieldsByStorage($validatedFields);

        // Store the validated settings in their storages
        foreach ($fieldsByStorage as $storageName => $fields) {
            $settingsToStore = $this->prepareFieldsForStorage($fields);

            // Attempt to save settings to storages
            try {
                $this->getStorage($storageName)->storeMultiple($settingsToStore);
            } catch (\Exception $e) {
                throw new StorageFailedException($e->getMessage());
            }
        }

        // Return the stored settings
        return $this->convertFieldsToSettings($validatedFields);
    }

    /**
     * Validate the settings and return their fields
     * @return Collection|Field[]
     * @throws ValidationFailedExceptions when validation fails
     */
    private function validateSettings(array $settings, \WP_REST_Request $request): Collection
    {
        $validationErrors = [];

        // Find the necessary fields to validate
        $fields = $this->fields->whereIn('name', array_keys($settings));

        foreach ($fields as $field) {
            $value = $settings[$field->getName()];

            // Validate the fields
            try {
                $field->validate($value, $request);
            } catch (ValidatorFailedException $e) {
                $validationErrors[$field->getName()] = $e;
                // Go to the next field when validation failed
                continue;
            }

            // Set the validated field value and add it to the validated data
            $field->setValue($value);
        }

        if (count($validationErrors) > 0) {
            throw new ValidationFailedExceptions($validationErrors);
        }

        return $fields;
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
     * Creates an associative array with the fields grouped by storage.
     */
    private function groupFieldsByStorage(Collection $fields): array
    {
        $fieldsByStorage = [];
        foreach ($fields as $field) {
            $storageName = $field->getStorage();
            if (!isset($fieldsByStorage[$storageName])) {
                $fieldsByStorage[$storageName] = new Collection([]);
            }
            $fieldsByStorage[$storageName]->push($field);
        }
        return $fieldsByStorage;
    }

    /**
     * Create a key/value array with the field names for storage as the key
     * @param Collection|Field[] $fields
     * @return array
     */
    protected function prepareFieldsForStorage(Collection $fields): array
    {
        $settings = [];
        foreach ($fields as $field) {
            $settings[$field->getNameForStorage()] = $field->getValue();
        }
        return $settings;
    }

    /**
     * Create a key/value array with the field name as the key
     * @param Collection|Field[] $fields
     */
    protected function convertFieldsToSettings(Collection $fields): array
    {
        $settings = [];
        foreach ($fields as $field) {
            $settings[$field->getName()] = $field->getValue();
        }
        return $settings;
    }
}