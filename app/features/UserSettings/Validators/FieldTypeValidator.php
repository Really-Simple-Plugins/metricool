<?php

namespace Metricool\Features\UserSettings\Validators;

use Metricool\Features\UserSettings\Exceptions\ValidatorFailedException;

class FieldTypeValidator extends AbstractValidator
{
    /**
     * Checks if the field value is valid based on its type
     * @inheritDoc
     */
    public function validate($value, \WP_REST_Request $request = null): void
    {
        if (!$this->isEmptyValue($value)) {
            return;
        }

        // Do the built-in validation
        switch ($this->field->getType()) {
            case 'boolean':
                if (!is_bool($value)) {
                    throw new ValidatorFailedException(__('Please enter a valid boolean', 'metricool'), $this);
                }
                break;
            case 'string':
                if (!is_string($value)) {
                    throw new ValidatorFailedException(__('Please enter a valid string', 'metricool'), $this);
                }
                break;
            case 'integer':
                if (!is_int($value)) {
                    throw new ValidatorFailedException(__('Please enter a valid integer', 'metricool'), $this);
                }
                break;
            case 'float':
                if (!is_float($value)) {
                    throw new ValidatorFailedException(__('Please enter a valid float', 'metricool'), $this);
                }
                break;
            case 'array':
            case 'object':
                if (!is_array($value)) {
                    throw new ValidatorFailedException(__('Please enter a valid array', 'metricool'), $this);
                }
                break;
        }
    }
}