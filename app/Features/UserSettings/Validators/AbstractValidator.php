<?php

declare(strict_types=1);

namespace Metricool\Features\UserSettings\Validators;

use Metricool\Support\Utility\StringUtility;
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
     * Validates the given value according to the rules of the validator.
     * @param mixed $value
     * @throws ValidatorFailedException
     */
    abstract public function validate($value, \WP_REST_Request $request): void;

    /**
     * Validates if the value is considered empty. If the value is considered a
     * boolean, it will never be considered empty. For strings, it will use the
     * {@see StringUtility::isEmptyValue} method to determine if the string is
     * empty. Falls back to PHP's empty() function for other types.
     * @param mixed $value
     */
    protected function isEmptyValue($value): bool
    {
        if (is_bool($value)) {
            return false;
        }

        if (is_string($value)) {
            return StringUtility::isEmptyValue($value);
        }

        return empty($value);
    }
}
