<?php

namespace Metricool\Http\Metricool\Entities;

use Metricool\Http\Metricool\MetricoolClient;

class Scheduler
{
    protected MetricoolClient $client;
    private string $endpoint = 'v2/scheduler/posts';

    public function __construct(MetricoolClient $client)
    {
        $this->client = $client;
    }

    public function createPost(array $data): array
    {
        return $this->client->post($this->endpoint, [
            'json' => $data
        ]);
    }

    public function sendToReview(array $data): array
    {
        return $this->client->put($this->endpoint, [
            'json' => $data
        ]);
    }
}