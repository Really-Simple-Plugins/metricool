<?php

namespace Metricool\Features\UserSettings\Storage;

use Metricool\Features\UserSettings\Storage\Exceptions\ClientRequiredException;

class CustomStorage extends AbstractStorage
{
    protected object $client;
    protected string $method;

    public function __construct($name, $config)
    {
        if (!isset($config['client'])) {
            throw new ClientRequiredException('Client is required for storage: ' . $name . '. Please add it to the config.');
        }

        $this->client = $config['client'];
        $this->method = $config['method'] ?? 'post';
        $this->casing = $config['casing'] ?? '';

        parent::__construct($name, $config);
    }

    /**
     * @inheritDoc
     */
    public function get(string $key)
    {
        $value = $this->getMultiple([$key]);

        return $value[$key];
    }

    /**
     * @inheritDoc
     */
    public function getMultiple(array $keys): array
    {
        $data = [];

        // Retrieve all values from the client
        $results = $this->client->get();

        // Retrieve the requested values from the response
        foreach ($keys as $key) {
            $data[$key] = $results[$this->convertCase($key)] ?? null;
        }

        // Return the requested values
        return $data;
    }

    /**
     * @inheritDoc
     */
    public function set(string $key, $value): void
    {
        $this->setMultiple([$key => $value]);
    }

    /**
     * @inheritDoc
     */
    public function setMultiple(array $settings): void
    {
        // Create the request data
        $requestData = [];
        foreach ($settings as $key => $value) {
            $requestData[$this->convertCase($key)] = $value;
        }

        // Send the request to the client
        $this->client->{$this->method}($requestData);
    }
}