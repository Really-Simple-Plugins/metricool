<?php

namespace Metricool\Features\UserSettings\Validators;

use Metricool\Features\UserSettings\Validators\Exceptions\ValidatorFailedException;

class BuiltInValidator extends AbstractValidator
{
    /**
     * @inheritDoc
     */
    public function validate($value, \WP_REST_Request $request = null): void
    {
        // Call the validateCallback when not null
        if (!is_null($this->field->validateCallback)) {
            ($this->field->validateCallback)($value, $request);
        }

        // Do the built-in validation
        switch ($this->field->getType()) {
            case 'boolean':
            case 'bool':
                // accept true, false, 0, and 1 and "1", "0", "true" or "false" as boolean values
                if (!is_bool($value) && !in_array($value, ['0', '1', 'true', 'false'])) {
                    throw new ValidatorFailedException(__('Please enter a valid boolean', 'metricool'));
                }
                break;
            case 'string':
                if (!is_string($value) && !is_numeric($value)) {
                    throw new ValidatorFailedException(__('Please enter a valid string', 'metricool'));
                }
                break;
            case 'integer':
            case 'int':
                if (!is_int($value)) {
                    throw new ValidatorFailedException(__('Please enter a valid number', 'metricool'));
                }
                break;
            case 'array':
                if (!is_array($value)) {
                    throw new ValidatorFailedException(__('Please enter a valid array', 'metricool'));
                }
                break;
        }
    }
}