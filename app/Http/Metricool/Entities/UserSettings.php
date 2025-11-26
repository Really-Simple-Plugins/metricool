<?php

declare(strict_types=1);

namespace Metricool\Http\Metricool\Entities;

use GuzzleHttp\Exception\GuzzleException;
use Metricool\Http\Metricool\MetricoolClient;

class UserSettings
{
    protected MetricoolClient $client;
    private string $endpoint = 'v2/settings/users/';

    public function __construct(MetricoolClient $client)
    {
        $this->client = $client;
        $this->endpoint = $this->endpoint . $client->getUserId();
    }

    /**
     * @throws GuzzleException
     */
    public function get(): array
    {
        $response = $this->client->get($this->endpoint);
        return ($response['data'] ?? []);
    }

    /**
     * @throws GuzzleException
     */
    public function patch(array $data): array
    {
        // Don't build the query parameters with http_build_query because we need
        // the "fields" query variable multiple times.
        $endpoint = $this->endpoint . '?fields=' . implode('&fields=', array_keys($data));
        $data = json_encode($data);

        $response = $this->client->patch($endpoint, $data);

        return ($response['data'] ?? []);
    }
}
