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