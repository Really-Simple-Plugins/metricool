<?php

namespace Metricool\Features\UserSettings;

use Metricool\Helpers\Collection;
use Metricool\Features\UserSettings\Fields\Field;
use Metricool\Features\UserSettings\Exceptions\StorageFailedException;
use Metricool\Features\UserSettings\Exceptions\ValidatorFailedException;
use Metricool\Features\UserSettings\Exceptions\ValidationFailedExceptions;

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
     * Validate and update settings and return their updated values
     * @throws ValidationFailedExceptions with all the validation errors when validation fails
     * @throws StorageFailedException when it fails to store data to it's storage
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
        }

        if (count($validationErrors) > 0) {
            throw new ValidationFailedExceptions($validationErrors);
        }

        // todo - decouple the response here. ->store() ->get() ?
        $response = new UserSettingsResponse($fields);
        return $response->parse()->get();
    }
}