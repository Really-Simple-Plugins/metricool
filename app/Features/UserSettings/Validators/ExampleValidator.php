<?php

declare(strict_types=1);

namespace Metricool\Features\UserSettings\Validators;

use Metricool\Features\UserSettings\Exceptions\ValidatorFailedException;

class ExampleValidator extends AbstractValidator
{
    /**
     * This validator checks if the value is 1
     */
    public function validate($value, \WP_REST_Request $request = null): void
    {
        if (!$this->isEmptyValue($value) && $value !== '1') {
            throw new ValidatorFailedException(__('This value must be 1'), $this);
        }
    }
}
