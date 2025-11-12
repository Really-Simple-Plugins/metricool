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
    /** @var Collection|Field[] */
    public Collection $fields;
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
     * Return an array of fields and their values
     * @return Field[]
     * @throws \Exception when data retrieval fails
     * @throws StorageFailedException when data was not present in storage
     */
    public function getSettings(array $fieldNames): array
    {
        $settings = [];

        // Find the necessary fields from the configuration
        $fields = $this->fields->whereIn('name', $fieldNames);

        // Group fields by storage to make it easier to retrieve all values with a single request
        $fieldsByStorage = $this->groupFieldsByStorage($fields->toArray());

        foreach ($fieldsByStorage as $storage => $fieldsToRetrieve) {
            // Retrieve the settings from storage
            $retrievedData = $this->getStorage($storage)
                ->getMultiple(array_keys($fieldsToRetrieve));

            foreach ($fieldsToRetrieve as $field) {
                // Check if the value is present in storage
                if (array_key_exists($field->getName(), $retrievedData)) {
                    $field->setValue($retrievedData[$field->getName()]);
                } else {
                    throw new StorageFailedException(
                        sprintf(
                            'Value could not be found for field: %s because it was not retrieved from storage: %s',
                            $field->getName(),
                            $storage
                        )
                    );
                }

                // Add the field to the results
                $settings[$field->name] = $field;
            }
        }

        return $this->getFieldValues($settings);
    }

    /**
     * Validate and update settings and return their updated values
     * @throws ValidationFailedExceptions with all the validation errors when validation fails
     * @throws StorageFailedException when it fails to store data to it's storage
     */
    public function storeSettings(array $settings, \WP_REST_Request $request): array
    {
        $validatedFields = $this->validateFields($settings, $request);
        $storedSettings = [];

        // Group the validated fields by storage to store all values in a single request
        $fieldsByStorage = $this->groupFieldsByStorage($validatedFields);

        // Store the validated settings in their storages
        foreach ($fieldsByStorage as $storageName => $fields) {
            $settingsToStore = $this->getFieldValues($fields);

            // Attempt to save settings to storages
            try {
                $this->getStorage($storageName)->setMultiple($settingsToStore);
            } catch (\Exception $e) {
                throw new StorageFailedException($e->getMessage());
            }

            // Push updated settings to the stored Settings
            $storedSettings[] = $settingsToStore;
        }

        // Return the stored settings
        return $storedSettings;
    }

    /**
     * Validate the fields and return their validated values.
     * @return Field[]
     * @throws ValidationFailedExceptions when validation fails
     */
    private function validateFields(array $settings, \WP_REST_Request $request): array
    {
        $validatedFields = [];
        $validationErrors = [];

        foreach ($settings as $fieldName => $value) {
            $field = $this->getField($fieldName);

            if ($field === null) {
                // Continue to the next field if the field is unknown
                continue;
            }

            // Validate the field and add stop validation on the first failure
            try {
                $field->validate($value, $request);
            } catch (ValidatorFailedException $e) {
                $validationErrors[$fieldName] = $e;
                continue;
            }

            // Set the validated field value and add it to the validated data
            $field->setValue($value);
            $validatedFields[$fieldName] = $field;
        }

        if (count($validationErrors) > 0) {
            throw new ValidationFailedExceptions($validationErrors);
        }

        return $validatedFields;
    }


    /**
     * Initialize storages from user_settings configuration
     */
    private function initializeStorages(array $storagesConfig): void
    {
        $storages = [];
        foreach ($storagesConfig as $name => $config) {
            $storages[$name] = StorageFactory::createFromConfig($name, $config);
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
            $field = FieldFactory::createFromConfig($name, $config);

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
     * Creates an associative array with the fields grouped by storage.
     */
    private function groupFieldsByStorage(array $fields): array
    {
        $fieldsByStorage = [];
        foreach ($fields as $field) {
            $storageName = $field->getStorage();
            if (!isset($fieldsByStorage[$storageName])) {
                $fieldsByStorage[$storageName] = [];
            }
            $fieldsByStorage[$storageName][$field->getName()] = $field;
        }
        return $fieldsByStorage;
    }

    /**
     * Create an array with the key/values of the fields
     * @param Field[] $fields
     */
    protected function getFieldValues(array $fields): array
    {
        return array_map(function (Field $field) {
            return $field->getValue();
        }, $fields);
    }
}