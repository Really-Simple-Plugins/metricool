<?php

namespace Metricool\Features\UserSettings\Storage;

class ApiStorage extends AbstractStorage
{
    protected object $client;
    protected string $method;

    public function __construct($name, $config)
    {
        $this->name = $name;
        $this->client = $config['client'];
        $this->method = $config['method'] ?? 'patch';
        $this->casing = $config['casing'] ?? 'camelCase';
    }

    /**
     * @throws \Exception
     */
    public function get(string $key)
    {
        $value = $this->getMultiple([$key]);

        return $value[$key];
    }

    /**
     * @throws \Exception
     */
    public function getMultiple(array $keys): array
    {
        $data = $this->client->get();

        $results = [];
        foreach ($keys as $key) {
            $results[$key] = $data[$this->convertCase($key)] ?? null;
        }

        return $results;
    }

    /**
     * @throws \Exception
     */
    public function set(string $key, $value): bool
    {
        return $this->setMultiple([$key => $value]);
    }

    /**
     * @throws \Exception
     */
    public function setMultiple(array $data)
    {
        $requestData = [];
        foreach ($data as $key => $value) {
            $requestData[$this->convertCase($key)] = $value;
        }

        return $this->client->{$this->method}($requestData);
    }

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

}