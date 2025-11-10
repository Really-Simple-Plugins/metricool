<?php

namespace Metricool\Features\UserSettings\Storage;

use Metricool\Features\UserSettings\Storage\Exceptions\StorageClientRequiredException;

class ApiStorage extends AbstractStorage
{
    protected object $client;
    protected string $method;

    public function __construct($name, $config)
    {
        if (!isset($config['client'])) {
            throw new StorageClientRequiredException('Client is required for API storage: ' . $name . '. Please add it to the config.');
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
        // Retrieve all values from the API client
        $data = $this->client->get();

        // Get the requested values from the API response
        $results = [];
        foreach ($keys as $key) {
            $results[$key] = $data[$this->convertCase($key)] ?? null;
        }

        // Return the requested values
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
        // Create the request data
        $requestData = [];
        foreach ($data as $key => $value) {
            $requestData[$this->convertCase($key)] = $value;
        }

        // Send the request to the API client
        return $this->client->{$this->method}($requestData);
    }
}