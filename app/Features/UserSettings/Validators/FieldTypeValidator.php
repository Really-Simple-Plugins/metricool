<?php

declare(strict_types=1);

namespace Metricool\Features\UserSettings\Validators;

use Metricool\Features\UserSettings\Exceptions\ValidatorFailedException;

class FieldTypeValidator extends AbstractValidator
{
    /**
     * Checks if the field value is valid based on its type
     * @param mixed $value
     * @throws ValidatorFailedException
     */
    public function validate($value, ?\WP_REST_Request $request = null): void
    {
        if ($this->isEmptyValue($value)) {
            return; // Allowed here, use RequiredValidator if needed
        }

        if ($this->field->isBoolean()) {
            if (is_bool($value)) {
                return; // valid
            }

            throw new ValidatorFailedException(esc_html__('Please enter a valid boolean', 'metricool'), $this);
        }

        if ($this->field->isInteger()) {
            if (is_int($value)) {
                return; // valid
            }

            throw new ValidatorFailedException(esc_html__('Please enter a valid integer', 'metricool'), $this);
        }

        if ($this->field->isFloat()) {
            if (is_float($value)) {
                return; // valid
            }

            throw new ValidatorFailedException(esc_html__('Please enter a valid float', 'metricool'), $this);
        }

        if ($this->field->isString()) {
            if (is_string($value)) {
                return; // valid
            }

            throw new ValidatorFailedException(esc_html__('Please enter a valid string', 'metricool'), $this);
        }

        if ($this->field->isArray()) {
            if (is_array($value)) {
                return; // valid
            }

            throw new ValidatorFailedException(esc_html__('Please enter a valid array', 'metricool'), $this);
        }

        if ($this->field->isObject()) {
            if (is_object($value)) {
                return; // valid
            }

            throw new ValidatorFailedException(esc_html__('Please enter a valid object', 'metricool'), $this);
        }

        throw new ValidatorFailedException(esc_html__('Please enter a valid value', 'metricool'), $this);
    }
}
