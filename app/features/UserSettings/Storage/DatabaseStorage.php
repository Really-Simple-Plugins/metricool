<?php

namespace Metricool\Features\UserSettings\Storage;

class DatabaseStorage extends AbstractStorage
{
    private string $prefix;

    public function __construct($name, array $config)
    {
        $this->prefix = $config['prefix'] ?? '';

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
        update_option($this->prefix . $this->convertCase($key), (string) $value);
    }

    public function setMultiple(array $data): bool
    {
        foreach ($data as $key => $value) {
            $this->set($key, $value);
        }

        return true;
    }
}