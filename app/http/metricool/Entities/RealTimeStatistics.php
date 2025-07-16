<?php

namespace Metricool\Http\Metricool\Entities;

use Metricool\Http\Metricool\MetricoolClient;

class RealTimeStatistics
{
    protected MetricoolClient $client;
    protected string $endpoint = 'stats/rt/';

    /**
     * The real time statistics API is compatible with these metrics.
     *
     * Possible values:
     * - 'values' — ['Visitors', 'PageViews', 'SessionsCount', 'activeVisits', 'reading']
     * - 'pvperhour' — Page views per hour for timeline usage
     * - 'sessions' — Object containing "sessions" and "timeline".
     *     Sessions: session data for the last 24 hours.
     *     Timeline: session data for timeline usage.
     * - 'distribution/referers' — Visitors per page for the last 24 hours.
     * - 'distribution/countries' — Visitors per country for the last 24 hours.
     * - 'distribution/sources' — Visitors per source for the last 24 hours.
     * - 'distribution/currentpageviews' — Pages currently viewed with amount of visitors.
     */
    private array $compatibleMetrics = [
        'values',
        'pvperhour',
        'sessions',
        'distribution/referers',
        'distribution/countries',
        'distribution/sources',
        'distribution/currentpageviews',
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

    public function get(): array
    {
        return $this->client->get($this->endpoint);
    }

    public function count(): int
    {
        return count($this->get());
    }

    public function sum(): int
    {
        $numericValues = array_filter($this->get(), 'is_numeric');
        return array_sum($numericValues);
    }
}