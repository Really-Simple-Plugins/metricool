<?php

declare(strict_types=1);

namespace Metricool\Features\UserSettings\Factories;

use Metricool\Features\UserSettings\Fields\Field;
use Metricool\Features\UserSettings\Validators\AbstractValidator;

class ValidatorFactory
{
    private const VALIDATORS_NAMESPACE = '\\Metricool\\Features\\UserSettings\\Validators\\';

    /**
     * Creates a validator instance from the configuration string.
     *
     * Example validator strings:
     * 'required', 'email', 'requiredIf:sendToAlternativeEmail,true'
     *
     * The name of the validator class will be converted to PascalCase,
     * and the Factory will try to find the class in the Validators namespace.
     */
    public static function createFromConfig(string $validatorConfig, Field $field): AbstractValidator
    {
        // Extract the name and parameters from the validator string
        $validatorInfo = self::parseValidatorConfig($validatorConfig);

        $validatorClass = self::VALIDATORS_NAMESPACE . ucfirst($validatorInfo['className']);
        if (!class_exists($validatorClass)) {
            throw new \InvalidArgumentException('Validator "' . esc_html($validatorClass) . '" not found');
        }

        return new $validatorClass($field, ...$validatorInfo ['params']);
    }

    /**
     * Parse a validator string into an array with the name and parameters
     * Example: "requiredIf:sendToAlternativeEmail,true"
     * Becomes: ['name' => 'requiredIf', 'params' => ['sendToAlternativeEmail', 'true']]
     * @see config/user_settings
     */
    protected static function parseValidatorConfig(string $validator): array
    {
        $parts = explode(':', $validator);

        return [
            'className' => $parts[0] . 'Validator',
            'params' => (count($parts) > 1) ? explode(',', $parts[1]) : [],
        ];
    }
}
