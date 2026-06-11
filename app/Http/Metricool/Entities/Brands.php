<?php

declare(strict_types=1);

namespace Metricool\Http\Metricool\Entities;

use GuzzleHttp\Exception\GuzzleException;
use Metricool\Http\Metricool\MetricoolClient;

class Brands
{
    protected MetricoolClient $client;
    private string $endpoint = 'v2/settings/brands/';

    public function __construct(MetricoolClient $client)
    {
        $this->client = $client;
    }

    /**
     * Stub method to get all connected brands
     * @throws GuzzleException
     */
    public function all(): array
    {
        $result = $this->client->get($this->endpoint);

        if (!isset($result['data'])) {
            return [];
        }

        return $result['data'];
    }

    /**
     * Stub method to get brand by id
     * @throws GuzzleException
     */
    public function get(string $id): array
    {
        $result = $this->client->get($this->endpoint . $id);

        if (!isset($result['data'])) {
            return [];
        }

        return $result['data'];
    }
}
