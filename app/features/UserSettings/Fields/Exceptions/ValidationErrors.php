<?php

namespace Metricool\Features\UserSettings\Fields\Exceptions;

use Metricool\Features\UserSettings\Validators\Exceptions\ValidatorFailedException;

/**
 * Is thrown when a field fails validation. It contains a list of validation errors
 */
class ValidationErrors extends \RuntimeException
{
    /** @var ValidatorFailedException */
    public $validationErrors = [];

    public function __construct(array $validationErrors)
    {
        $this->validationErrors = $validationErrors;

        parent::__construct('Field validation failed');
    }

    public function getValidationErrors()
    {
        return $this->validationErrors;
    }
}