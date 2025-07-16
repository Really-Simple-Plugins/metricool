<?php

namespace Metricool\Http\Metricool\Entities;

use Metricool\Http\Metricool\MetricoolClient;
use Metricool\Http\Metricool\Traits\isFilterable;

class TimelineStatistics
{
    use isFilterable;

    protected MetricoolClient $client;
    protected string $endpoint = 'stats/timeline/';

    /**
     * The timeline statistics API is compatible with these metrics.
     */
    private array $compatibleMetrics = [
        'PageViews',
        'SessionsCount',
        'Visitors',
        'DailyPosts',
        'DailyComments',
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
            'start' => '/^\d+$/',
            'end' => '/^\d+$/',
        ];
    }

    public function get(): array
    {
        return $this->client->get($this->endpoint);
    }
}