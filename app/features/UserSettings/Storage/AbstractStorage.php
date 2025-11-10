<?php

namespace Metricool\Features\UserSettings\Storage;

use Metricool\Utility\StringUtility;

abstract class AbstractStorage
{
    public string $name;
    protected string $casing = 'snake_case';

    public function __construct($name, array $config)
    {
        $this->name = $name;
        $this->casing = $config['casing'] ?? 'snake_case';
    }

    abstract public function get(string $key);

    abstract public function getMultiple(array $keys);

    abstract public function set(string $key, $value);

    abstract public function setMultiple(array $data);

    public function sanitizeValue($value, string $type)
    {
        switch ($type) {
            case 'boolean':
            case 'bool':
                return $value ? 1 : 0;
            case 'email':
                return sanitize_email($value);
            case 'string':
                return sanitize_text_field($value);
            case 'array':
                return is_array($value) ? array_map('sanitize_text_field', $value) : [];
            case 'integer':
            case 'int':
                return filter_var($value, FILTER_VALIDATE_INT);
            default:
                return $value;
        }
    }

    protected function convertCase(string $key): string
    {
        switch ($this->casing) {
            case 'pascal_case':
                return StringUtility::snakeToPascalCase($key);
            case 'camel_case':
                return StringUtility::snakeToCamelCase($key);
            case 'snake_case':
                return StringUtility::camelToSnakeCase($key);
            default:
                return $key;
        }
    }
}