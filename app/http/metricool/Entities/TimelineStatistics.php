<?php

namespace Metricool\Http\Metricool\Entities;

use Carbon\Carbon;
use Metricool\Http\Metricool\MetricoolClient;
use Metricool\Http\Metricool\Traits\isFilterable;

/**
 * API responses for the timeline statistics contain of array entries where each
 * entry reflects a timestamp and its corresponding value for the metric.
 * Example: [
 *  "1752170400000",
 *  "981.0"
 * ]
 */
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
            'start' => '/^\d+$/',
            'end' => '/^\d+$/',
        ];
    }

    /**
     * Fetch and return the timeline statistics data plainly from the API.
     */
    public function get(): array
    {
        if ($this->requiresFilter && $this->filtered === false) {
            $this->filter($this->filters);
        }

        $cacheName = 'timeline_statistics_' . $this->endpoint;
        if ($cache = wp_cache_get($cacheName, 'metricool')) {
            return $cache;
        }

        $response =  $this->client->get($this->endpoint);

        wp_cache_set($cacheName, $response, 'metricool', MINUTE_IN_SECONDS);
        return $response;
    }
}