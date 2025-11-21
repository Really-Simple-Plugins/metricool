<?php

namespace Metricool\Features\UserSettings\Validators;

use Metricool\Utility\StringUtility;
use Metricool\Features\UserSettings\Fields\Field;
use Metricool\Features\UserSettings\Exceptions\ValidatorFailedException;

abstract class AbstractValidator
{
    protected Field $field;

    public function __construct(Field $field)
    {
        $this->field = $field;
    }

    /**
     * @throws ValidatorFailedException
     */
    abstract function validate($value, \WP_REST_Request $request);

    /**
     * Validates if the value is considered empty. This uses
     * {@see StringUtility::isEmptyValue} for string values.
     * @param mixed $value
     */
    protected function isEmptyValue($value): bool
    {
        return is_string($value) ? StringUtility::isEmptyValue($value) : empty($value);
    }
}
