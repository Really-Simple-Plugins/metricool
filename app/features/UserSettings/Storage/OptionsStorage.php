<?php

namespace Metricool\Features\UserSettings\Storage;

use http\Exception\InvalidArgumentException;

class OptionsStorage extends AbstractStorage
{
    private string $prefix;

    public function __construct($name, array $config)
    {
        if (!isset($config['prefix'])) {
            throw new InvalidArgumentException('Prefix is required for OptionsStorage: ' . $name);
        }

        $this->prefix = $config['prefix'];
        $this->casing = $config['casing'] ?? 'snakeCase';

        parent::__construct($name, $config);
    }

    public function get(string $key)
    {
        return get_option($this->prefix . $key) ?? null;
    }

    public function getMultiple(array $keys): array
    {
        $result = [];
        foreach ($keys as $key) {
            $result[$key] = $this->get($this->convertCase($key));
        }
        return $result;
    }

    public function set(string $key, $value): void
    {
        update_option($this->prefix . $this->convertCase($key), $value);
    }

    public function setMultiple(array $data): void
    {
        foreach ($data as $key => $value) {
            $this->set($key, $value);
        }
    }
}