<?php

namespace Metricool\Features\UserSettings\Exceptions;

use Metricool\Features\UserSettings\Validators\Exceptions\ValidatorFailedException;

/**
 * Is thrown when a storing setting fails validation. It contains a list of all the
 * validation errors
 */
class ValidationFailedExceptions extends \RuntimeException
{
    /** @var ValidatorFailedException */
    public $validationErrors = [];

    public function __construct(array $validationErrors)
    {
        $this->validationErrors = $validationErrors;

        parent::__construct('The validation of the settings failed');
    }

    public function getErrors(): array
    {
        $errors = [];
        foreach ($this->validationErrors as $fieldName => $error) {
            $errors[$fieldName] = [
                'message' => $error->getMessage()
            ];
        }
        return $errors;
    }
}