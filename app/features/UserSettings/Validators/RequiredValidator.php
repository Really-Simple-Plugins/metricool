<?php

namespace Metricool\Features\UserSettings\Validators;

use Metricool\Features\UserSettings\Exceptions\ValidatorFailedException;

class RequiredValidator extends AbstractValidator
{
    /**
     * Checks if the field is required and is not empty
     * @inheritDoc
     */
    public function validate($value, \WP_REST_Request $request = null): void
    {
        if ($this->isEmptyValue($value)) {
            throw new ValidatorFailedException(__('Please enter a value', 'metricool'), $this);
        }
    }
}