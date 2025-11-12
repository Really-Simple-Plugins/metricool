<?php

namespace Metricool\Features\UserSettings\Validators;

use Metricool\Features\UserSettings\Validators\Exceptions\ValidatorFailedException;

class EmailValidator extends AbstractValidator
{
    /**
     * Checks if the field contains a valid email
     * @inheritDoc
     */
    public function validate($value, \WP_REST_Request $request = null): void
    {
        if (!$this->isEmptyValue($value) && is_email($value) === false) {
            throw new ValidatorFailedException(__('Please enter a valid email', 'metricool'), $this);
        }
    }
}