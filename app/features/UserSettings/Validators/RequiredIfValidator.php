<?php

namespace Metricool\Features\UserSettings\Validators;

use Metricool\Features\UserSettings\Fields\Field;

class RequiredIfValidator extends AbstractValidator
{
    public string $requiredParam;
    /** @var mixed */
    public $requiredValue;

    public function __construct(Field $field, $requiredParam, $requiredValue)
    {
        $this->requiredParam = $requiredParam;
        $this->requiredValue = $requiredValue;

        parent::__construct($field);
    }

    /**
     * Checks if the required param matches the required value in the request and then validates the field
     * with the RequiredValidator
     * @inheritDoc
     */
    public function validate($value, \WP_REST_Request $request): void
    {
        if ($this->shouldValidate($request)) {
            $requiredValidator = new RequiredValidator($this->field);
            $requiredValidator->validate($value, $request);
        }
    }

    /**
     * Checks if the required param matches the required value in the request
     */
    protected function shouldValidate(\WP_REST_Request $request): bool
    {
        return $request->get_param($this->requiredParam) == $this->requiredValue;
    }
}