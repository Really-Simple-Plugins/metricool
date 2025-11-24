<?php

namespace Metricool\Features\UserSettings\Storage;

use Metricool\Features\UserSettings\Exceptions\ClientRequiredException;
use Metricool\Features\UserSettings\Interfaces\SubmittableStorageInterface;

/**
 * This storage uses a client to store and retrieve the UserSettings
 */
class RemoteStorage extends AbstractStorage implements SubmittableStorageInterface
{
    protected object $client;
    protected string $method;

    /**
     * This property is used to avoid multiple requests to the remote client.
     * It stores the settings retrieved from the client and any changes made
     * to them before submitting.
     */
    protected array $settings = [];

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
        if (!empty($this->settings)) {
            $settingsKey = $this->convertCase($key);
            return $this->settings[$settingsKey] ?? null;
        }

        $value = $this->getMultiple([$key]);
        return $value[$key];
    }

    /**
     * @inheritDoc
     */
    public function getMultiple(array $keys): array
    {
        $data = [];

        // Retrieve all values from the client for the first time
        if (empty($this->settings)) {
            $this->settings = $this->client->get();
        }

        // Retrieve the requested values from the response
        foreach ($keys as $key) {
            $data[$key] = $this->settings[$this->convertCase($key)] ?? null;
        }

        // Return the requested values
        return $data;
    }

    /**
     * Store the given key and value in the local settings array. Call
     * {@see submit()} to send the changes to the remote client.
     * @throws \InvalidArgumentException when the key could not be converted
     */
    public function store(string $key, $value): void
    {
        $this->settings[$this->convertCase($key)] = $value;
    }

    /**
     * Store the given key and value pairs in the local settings array. Call
     * {@see submit()} to send the changes to the remote client.
     * @throws \InvalidArgumentException when the key could not be converted
     */
    public function storeMultiple(array $settings): void
    {
        foreach ($settings as $key => $value) {
            $this->store($key, $value);
        }
    }

    /**
     * Submit the stored settings to the remote client with the {@see method}
     * defined in the config. Returns silently if there are no settings to
     * submit.
     * @throws \Exception when the submission fails
     */
    public function submit(): void
    {
        if (empty($this->settings)) {
            return;
        }

        $this->client->{$this->method}($this->settings);
    }
}