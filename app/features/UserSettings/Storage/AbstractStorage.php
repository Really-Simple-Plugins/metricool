<?php

namespace Metricool\Features\UserSettings\Storage;

use Metricool\Utility\StringUtility;

abstract class AbstractStorage
{
    public string $name;
    protected string $casing;

    public function __construct($name, array $config)
    {
        $this->name = $name;
    }

    abstract public function get(string $key);

    abstract public function getMultiple(array $keys);

    abstract public function set(string $key, $value);

    abstract public function setMultiple(array $data);

    protected function convertCase(string $key): string
    {
        switch ($this->casing) {
            case 'pascalCase':
                return StringUtility::snakeToPascalCase($key);
            case 'camelCase':
                return StringUtility::snakeToCamelCase($key);
            case 'snakeCase':
                return StringUtility::camelToSnakeCase($key);
            case '':
                return $key;
            default:
                throw new \InvalidArgumentException('Unknown casing type: ' . $this->casing . ' for storage: ' . $this->name . '.');
        }
    }
}