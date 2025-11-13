<?php

namespace Metricool\Features\UserSettings\Storage;

use Metricool\Utility\StringUtility;

/**
 * Storage is responsible for storing and retrieving settings
 */
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
     * @throws \Exception when the value could not be stored
     */
    abstract public function store(string $key, $value);

    /**
     * Store multiple settings
     * @throws \Exception when one of the values could not be stored
     */
    abstract public function storeMultiple(array $settings);

    /**
     * Converts the casing to storage casing
     * @throws \InvalidArgumentException when the casing is unknown
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