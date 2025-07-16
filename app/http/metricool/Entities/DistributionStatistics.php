<?php

namespace Metricool\Http\Metricool\Entities;

use Metricool\Http\Metricool\MetricoolClient;
use Metricool\Http\Metricool\Traits\isFilterable;

class DistributionStatistics
{
    use isFilterable;

    protected MetricoolClient $client;
    protected string $endpoint = 'stats/distribution/';

    /**
     * The distribution statistics API is compatible with these metrics.
     */
    private array $compatibleMetrics = [
        'country',
        'referers',
        'sources',
    ];

    /**
     * Pass a compatible metric to the constructor: {@see compatibleMetrics}
     * @throws \InvalidArgumentException
     */
    public function __construct(MetricoolClient $client, string $metric)
    {
        if (!in_array($metric, $this->compatibleMetrics)) {
            throw new \InvalidArgumentException("Incompatible metric given: $metric");
        }

        $this->client = $client;
        $this->endpoint .= $metric;
    }

    /**
     * @inheritDoc
     */
    protected function getAcceptedFilters(): array
    {
        return [
            'start' => '/^\d+$/', // Just digits
            'end' => '/^\d+$/', // Just digits
            'country' => '/^[a-z]{2}$/', // ISO 3166-1 alpha-2 lowercase country code
        ];
    }

    public function get(): array
    {
        return $this->client->get($this->endpoint);
    }
}