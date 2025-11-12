<?php

namespace Metricool\Features\UserSettings\Validators;

use Metricool\Features\UserSettings\Validators\Exceptions\ValidatorFailedException;

class ExampleValidator extends AbstractValidator
{
    /**
     * @inheritDoc
     */
    public function validate($value, \WP_REST_Request $request = null): void
    {
        if ($value !== '1') {
            throw new ValidatorFailedException(__('This value must be 1'));
        }
    }
}
