<?php

namespace Metricool\Features\UserSettings\Factories;

use Metricool\Features\UserSettings\Fields\Field;

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
}