<?php

namespace Metricool\Http\Metricool\Entities;

use GuzzleHttp\Exception\GuzzleException;
use Metricool\Helpers\Event;
use Metricool\Http\Metricool\MetricoolClient;

class ConnectedBrands
{
    protected MetricoolClient $client;
    private string $endpoint = 'v2/settings/brands/';

    /**
     * @throws \Exception
     */
    public function __construct(MetricoolClient $client)
    {
        $this->client = $client;
        $this->endpoint = $this->endpoint . $this->client->getBlogId();
    }

    /**
     * Stub method to get all connected brands via {@see all()}.
     * @throws GuzzleException
     */
    public function get(): array
    {
        if (!$this->client->hasBlogId()) {
            // This endpoint should not be called without a BlogId. Return empty result.
            return [];
        }

        $result = $this->client->get($this->endpoint);

        if (!isset($result['data']['networksData'])) {
            return [];
        }

        Event::dispatch(Event::CONNECTED_NETWORKS_DATA_LOADED, $result['data']['networksData']);

        return $result['data'];
    }
}