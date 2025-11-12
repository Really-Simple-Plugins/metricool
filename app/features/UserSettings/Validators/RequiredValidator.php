<?php

namespace Metricool\Features\UserSettings\Validators;

use Metricool\Features\UserSettings\Validators\Exceptions\ValidatorFailedException;

class RequiredValidator extends Validator
{
    /**
     * Checks if the field is required and is not empty
     * @inheritDoc
     */
    public function validate($value, \WP_REST_Request $request = null): void
    {
        if (($value === '' || is_null($value))) {
            throw new ValidatorFailedException(__('Please enter a value', 'metricool'));
        }
    }
}