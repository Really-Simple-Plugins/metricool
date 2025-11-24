<?php

namespace Metricool\Features\UserSettings;

use Metricool\Helpers\Collection;
use Metricool\Features\UserSettings\Fields\Field;
use Metricool\Features\UserSettings\Exceptions\StorageSubmitException;
use Metricool\Features\UserSettings\Exceptions\ValidatorFailedException;
use Metricool\Features\UserSettings\Exceptions\ValidationFailedExceptions;
use Metricool\Features\UserSettings\Interfaces\SubmittableStorageInterface;

/**
 * This service is responsible for storing and retrieving user settings.
 */
class UserSettingsService
{
    /**
     * The fields from config/user_settings.php converted to {@see Field}
     * instances grouped in a Collection
     * @var Collection|Field[]
     */
    public Collection $fields;

    /**
     * A map of submittable storages to prevent multiple submissions to the
     * same storage.
     * @var array<string, SubmittableStorageInterface>
     */
    private array $submittableStorages = [];

    public function __construct(Collection $fields)
    {
        $this->fields = $fields;
    }

    /**
     * Return an array with keys and values of all the settings, optionally
     * filtered by section.
     * @throws \Exception
     */
    public function getSettingsResponse(?string $section = null): array
    {
        $response = new UserSettingsResponse($this->fields);

        if (!empty($section)) {
            $response->setSection($section);
        }

        return $response->parse()->get();
    }

    /**
     * Validate and update settings and return their updated values. If any
     * field storage is submittable, it will be submitted after all fields are
     * validated.
     *
     * @throws ValidationFailedExceptions with all the validation errors when validation fails
     * @throws StorageSubmitException when it fails to store data to it's storage
     */
    public function storeSettings(array $settings, \WP_REST_Request $request): array
    {
        $validationErrors = [];
        $fields = $this->fields->whereIn('name', array_keys($settings));

        foreach ($fields as $field) {
            $value = $settings[$field->getName()];

            try {
                $field->setValue($value, $request);
            } catch (ValidatorFailedException $e) {
                $validationErrors[$field->getName()] = $e;
                continue;
            }

            if ($field->storage instanceof SubmittableStorageInterface) {
                $this->setSubmittableStorage($field, $field->getStorage());
            }
        }

        if (count($validationErrors) > 0) {
            throw new ValidationFailedExceptions($validationErrors);
        }

        // No validation errors - submit the submittable storages
        $this->submitSubmittableStorages();

        $response = new UserSettingsResponse($fields);
        return $response->parse()->get();
    }

    /**
     * Map the given submittable storage to prevent multiple submissions to the
     * same storage. For example, when {@see RemoteStorage} is used for multiple
     * fields, we only want to call {@see RemoteStorage::submit()} once.
     */
    private function setSubmittableStorage(Field $field, SubmittableStorageInterface $storage)
    {
        if (!empty($this->submittableStorages[$field->getStorageName()])) {
            return;
        }

        $this->submittableStorages[$field->getStorageName()] = $storage;
    }

    /**
     * Submit all mapped submittable storages. Collects any errors and throws
     * a single exception if any storage submission fails.
     * @throws StorageSubmitException when any storage submission fails - is
     * caught in {@see UserSettingsEndpoint} and the message we return here is
     * only shown to the user has WP_DEBUG set to true.
     */
    private function submitSubmittableStorages(): void
    {
        $requestErrors = [];
        foreach ($this->submittableStorages as $storage) {
            try {
                $storage->submit();
            } catch (\Exception $e) {
                $requestErrors[$storage->name] = $e->getMessage();
                continue; // So we can try to submit other storages
            }
        }

        if (count($requestErrors) > 0) {
            $exception = new StorageSubmitException(__('Something went wrong while submitting the settings!', 'metricool'));
            $exception->setErrors($requestErrors);
            throw $exception;
        }
    }
}