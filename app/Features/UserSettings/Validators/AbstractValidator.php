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
     * Validates if the value is considered empty. This uses
     * {@see StringUtility::isEmptyValue} for string values.
     * @param mixed $value
     */
    protected function isEmptyValue($value): bool
    {
        return is_string($value) ? StringUtility::isEmptyValue($value) : empty($value);
    }
}
