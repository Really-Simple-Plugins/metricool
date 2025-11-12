<?php

namespace Metricool\Features\UserSettings\Factories;

use Metricool\Features\UserSettings\Fields\Field;

class ValidatorFactory
{
    private const VALIDATORS_NAMESPACE = '\\Metricool\\Features\\UserSettings\\Validators\\';

    public static function create(string $validator, Field $field)
    {
        $validatorClass = self::VALIDATORS_NAMESPACE . ucfirst($validator) . 'Validator';

        if (!class_exists($validatorClass)) {
            throw new \InvalidArgumentException("Validator '$validator' not found");
        }

        return new $validatorClass($field);
    }
}