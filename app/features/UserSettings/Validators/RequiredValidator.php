<?php

namespace Metricool\Features\UserSettings\Validators;

use Metricool\Features\UserSettings\Validators\Exceptions\ValidatorFailedException;

class RequiredValidator extends AbstractValidator
{
    /**
     * Checks if the field is required and is not empty
     * @inheritDoc
     */
    public function validate($value, \WP_REST_Request $request = null): void
    {
        var_dump($value);
        var_dump($this->isEmptyValue($value));
        exit;
        if ($this->isEmptyValue($value)) {
            throw new ValidatorFailedException(__('Please enter a value', 'metricool'), $this);
        }
    }
}