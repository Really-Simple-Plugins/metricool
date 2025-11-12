<?php

namespace Metricool\Features\UserSettings\Factories;

use Metricool\Features\UserSettings\Fields\Field;

class FieldFactory
{
    private const FIELDS_NAMESPACE = '\\Metricool\\Features\\UserSettings\\Fields\\';

    public static function create(string $name, array $config): Field
    {
        $fieldClassName = (isset($config['field']) && $config['field'] !== 'Field')
            ? ucfirst($config['field'] . 'Field')
            : 'Field';


        $fieldClass = self::FIELDS_NAMESPACE . $fieldClassName;

        if (!class_exists($fieldClass)) {
            throw new \InvalidArgumentException('Field "' . $fieldClass . '" not found');
        }

        return new $fieldClass($name, $config);
    }
}