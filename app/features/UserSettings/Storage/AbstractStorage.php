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

    /**
     * Retrieve a value from storage
     * @throws \Exception when the value could not be retrieved
     */
    abstract public function get(string $key);

    /**
     * Retrieve multiple values from storage
     * @throws \Exception when a value could not be retrieved
     */
    abstract public function getMultiple(array $keys);

    /**
     * Store a setting
     * @throws \Exception
     */
    abstract public function set(string $key, $value);

    /**
     * Store multiple settings
     * @throws \Exception
     */
    abstract public function setMultiple(array $settings);

    /**
     * Converts the casing to storage casing
     * @throws \Exception
     */
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