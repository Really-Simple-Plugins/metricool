<?php

namespace Metricool\Http\Metricool\Entities;

use GuzzleHttp\Exception\GuzzleException;
use Metricool\Helpers\Event;
use Metricool\Http\Metricool\MetricoolClient;

class ConnectedBrands
{
    protected MetricoolClient $client;
    private string $endpoint = 'v2/settings/brands/';

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
            // This endpoint should not be called without a BlogId. Return an empty result.
            return [];
        }

        $result = $this->client->get($this->endpoint);

        if (!isset($result['data'])) {
            return [];
        }

        // Dispatch event to notify about the connected social networks?
        if (isset($result['data']['networksData'])) {
            $this->dispatchNetworksDataLoaded($result['data']['networksData']);
        }

        return $result['data'];
    }

    /**
     * Dispatch the CONNECTED_SOCIAL_NETWORKS_DATA_LOADED event to notify about
     * the connected social networks
     */
    protected function dispatchNetworksDataLoaded(array $networksData): void
    {
        // get just the connection names
        $connectionNames = array_keys($networksData);
        
        // filter out networks that are not social media (webData, googleAds, etc)
        $connectedSocialNetworks = array_filter($connectionNames, function ($connectionName) {
            return !str_contains('webData', $connectionName) && !str_contains('Ads', $connectionName);
        });

        Event::dispatch(Event::CONNECTED_SOCIAL_NETWORKS_DATA_LOADED, $connectedSocialNetworks);
    }
}