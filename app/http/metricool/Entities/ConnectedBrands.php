<?php

namespace Metricool\Http\Metricool\Entities;

use GuzzleHttp\Exception\GuzzleException;
use Metricool\Http\Metricool\MetricoolClient;

class ConnectedBrands
{
    protected MetricoolClient $client;
    private string $endpoint = 'admin/profiles-auth';

    public function __construct(MetricoolClient $client)
    {
        $this->client = $client;
    }

    /**
     * Fetch and return the timeline statistics data plainly from the API.
     * @throws GuzzleException
     */
    public function all(): array
    {
        return $this->client->get($this->endpoint);
    }

    /**
     * Stub method to get all connected brands via {@see all()}.
     * @throws GuzzleException
     */
    public function get(): array
    {
        return $this->all();
    }
}