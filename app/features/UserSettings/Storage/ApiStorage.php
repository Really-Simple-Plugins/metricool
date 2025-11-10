<?php

namespace Metricool\Features\UserSettings\Storage;

use StorageRequiredException;

class ApiStorage extends AbstractStorage
{
    protected object $client;
    protected string $method;

    public function __construct($name, $config)
    {
        if (!isset($config['client'])) {
            throw new StorageRequiredException('Client is required for API storage: ' . $name);
        }

        $this->client = $config['client'];
        $this->method = $config['method'] ?? 'post';

        parent::__construct($name, $config);
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
}