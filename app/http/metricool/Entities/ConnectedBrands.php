<?php

namespace Metricool\Http\Metricool\Entities;

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
     */
    public function all(): array
    {
        return $this->client->get($this->endpoint);
    }
}