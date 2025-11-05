<?php

namespace Metricool\Features\UserSettings\Storage;

class DatabaseStorage extends AbstractStorage
{
    private string $prefix;

    public function __construct($name, array $config)
    {
        $this->name = $name;
        $this->prefix = $config['prefix'] ?? '';
    }

    public function get(string $key)
    {
        return get_option($this->prefix . $key) ?? null;
    }

    public function getMultiple(array $keys): array
    {
        $result = [];
        foreach ($keys as $key) {
            $result[$key] = $this->get($key);
        }
        return $result;
    }

    /**
     * @throws \Exception
     */
    public function set(string $key, $value): void
    {
        update_option($this->prefix . $key, (string) $value);
    }

    /**
     * @throws \Exception
     */
    public function setMultiple(array $data): bool
    {
        foreach ($data as $key => $value) {
            $this->set($key, $value);
        }

        return true;
    }

    public function sanitizeValue($value, string $type)
    {
        switch ($type) {
            case 'boolean':
            case 'bool':
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);
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
}