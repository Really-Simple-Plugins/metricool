<?php

namespace Metricool\Http\Metricool\Entities;

use GuzzleHttp\Exception\GuzzleException;
use Metricool\Http\Metricool\MetricoolClient;

class ConnectedBrands
{
    protected MetricoolClient $client;
    private string $endpoint = 'v2/settings/brands/';

    public function __construct(MetricoolClient $client)
    {
        $this->client = $client;
        if (defined('METRICOOL_BLOG_ID') && !empty(METRICOOL_BLOG_ID)) {
            $this->endpoint = $this->endpoint . METRICOOL_BLOG_ID;
        }
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