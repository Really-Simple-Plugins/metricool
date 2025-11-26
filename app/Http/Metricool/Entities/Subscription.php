<?php

declare(strict_types=1);

namespace Metricool\Http\Metricool\Entities;

use Metricool\Support\Helpers\Event;
use Metricool\Http\Metricool\MetricoolClient;

class Subscription
{
    protected MetricoolClient $client;
    private string $endpoint = 'v2/settings/subscriptions/current';

    public function __construct(MetricoolClient $client)
    {
        $this->client = $client;
    }

    public function get(): array
    {
        $response = $this->client->get($this->endpoint);
        $data = ($response['data'] ?? []);

        Event::dispatch(Event::SUBSCRIPTION_DATA_LOADED, $data);

        return $data;
    }
}
