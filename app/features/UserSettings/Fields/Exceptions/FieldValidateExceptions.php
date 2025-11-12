<?php

namespace Metricool\Features\UserSettings\Fields\Exceptions;

use Metricool\Features\UserSettings\Validators\Exceptions\ValidatorFailedException;

class FieldValidateExceptions extends \RuntimeException
{
    /** @var ValidatorFailedException */
    public $validationErrors = [];

    public function __construct(array $validationErrors)
    {
        $this->validationErrors = $validationErrors;

        parent::__construct('Field validation failed');
    }
}