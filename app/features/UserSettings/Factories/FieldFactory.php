<?php

namespace Metricool\Features\UserSettings\Factories;

use Metricool\Features\UserSettings\Fields\Field;
use Metricool\Features\UserSettings\Validators\FieldTypeValidator;

class FieldFactory
{
    private const FIELDS_NAMESPACE = '\\Metricool\\Features\\UserSettings\\Fields\\';

    /**
     * Creates a field from the user_settings configuration
     * @see config/user_settings.php
     */
    public static function createFromConfig(string $name, array $config): Field
    {
        $fieldClassName = (isset($config['field']))
            ? $config['field']
            : 'Field';

        $fieldClass = self::FIELDS_NAMESPACE . $fieldClassName;

        if (!class_exists($fieldClass)) {
            throw new \InvalidArgumentException('Field "' . $fieldClass . '" not found');
        }

        $field = new $fieldClass($name);
        $field->applyConfig($config);

        return $field;
    }

    /**
     * Create validators for a field from the configuration and add the TypeValidator if needed
     */
    public static function createValidatorsForField($field, array $validatorNames, bool $typeValidator): array
    {
        $validators = [];

        if ($typeValidator) {
            $validators[] = new FieldTypeValidator($field);
        }

        foreach ($validatorNames as $validator) {
            $validators[] = ValidatorFactory::create($validator, $field);
        }
        return $validators;
    }
}