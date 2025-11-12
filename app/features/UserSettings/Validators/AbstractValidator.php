<?php

namespace Metricool\Features\UserSettings\Validators;

use Metricool\Features\UserSettings\Fields\Field;
use Metricool\Features\UserSettings\Validators\Exceptions\ValidatorFailedException;
use Metricool\Utility\StringUtility;

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

    protected function isEmptyValue($value): bool
    {
        return StringUtility::isEmptyValue($value);
    }
}
