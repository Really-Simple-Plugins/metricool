<?php

namespace Metricool\Features\UserSettings\Factories;

use Metricool\Features\UserSettings\Fields\Field;

class ValidatorFactory
{
    private const VALIDATORS_NAMESPACE = '\\Metricool\\Features\\UserSettings\\Validators\\';

    public static function create(string $validator, Field $field)
    {
        // Extract the name and parameters from the validator string
        $validator = self::parseValidatorString($validator);

        $validatorClass = self::VALIDATORS_NAMESPACE . ucfirst($validator['name']) . 'Validator';

        if (!class_exists($validatorClass)) {
            throw new \InvalidArgumentException('Validator "' . $validator['name'] . '" not found');
        }

        return new $validatorClass($field, ...$validator['params']);
    }

    /**
     * Parse a validator string into an array with a name and parameters
     * Example string: "requiredIf:sendToAlternativeEmail,true"
     */
    protected static function parseValidatorString(string $validator): array
    {
        $parts = explode(':', $validator);

        return [
            'name' => $parts[0],
            'params' => (count($parts) > 1) ? explode(',', $parts[1]) : [],
        ];
    }
}