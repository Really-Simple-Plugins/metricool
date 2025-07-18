<?php

namespace Metricool\Http\Metricool\Entities;

use Carbon\Carbon;
use Metricool\Http\Metricool\MetricoolClient;
use Metricool\Http\Metricool\Traits\isFilterable;

/**
 * API responses for distribution statistics include data on how various metrics
 * are distributed. Such as page views by country, referrer pages, or traffic
 * sources.
 */
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
    public function __construct(MetricoolClient $client, string $metric, bool $filterRequired = true)
    {
        if (!in_array($metric, $this->compatibleMetrics)) {
            throw new \InvalidArgumentException("Incompatible metric given: $metric");
        }

        $this->client = $client;
        $this->endpoint .= $metric;
        $this->requiresFilter = $filterRequired;

        /**
         * The distribution statistics API need a filter by default to prevent
         * Internal Server errors on the remote server. We set the default
         * filters to the last 30 days.
         */
        $this->filters = [
            'start' => Carbon::now()->subDays(30)->format('Ymd'),
            'end' => Carbon::now()->format('Ymd'),
        ];
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

    /**
     * Fetch and return the distribution statistics data plainly from the API.
     */
    public function get(): array
    {
        if ($this->requiresFilter && $this->filtered === false) {
            $this->filter($this->filters);
        }

        return $this->client->get($this->endpoint);
    }
}