<?php

namespace Metricool\Http\Metricool\Entities;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Metricool\Http\Metricool\MetricoolClient;
use Metricool\Http\Metricool\Dto\Statistic;
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

    protected string $metric;
    protected MetricoolClient $client;
    public string $endpoint = 'stats/timeline/';

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
        $this->metric = $metric;

        if (!in_array($this->metric, $this->compatibleMetrics)) {
            throw new \InvalidArgumentException("Incompatible metric given: $this->metric");
        }

        $this->client = $client;
        $this->endpoint .= $this->metric;
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
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @return Collection<Statistic>
     */
    public function get(): Collection
    {
        if ($this->requiresFilter && $this->filtered === false) {
            $this->filter($this->filters);
        }

        $cacheName = 'timeline_statistics_' . $this->endpoint;
        if ($cache = wp_cache_get($cacheName, 'metricool')) {
            return $cache;
        }

        $response = $this->client->get($this->endpoint);

        /**
         * When this endpoint holds no data, Metricool returns a result with
         * non-standard output. Just return an empty response when only 1 row is
         * found in the results
         */
        if (is_array($response) && count($response) == 1) {
            // ASK: is early return preferred code style?
            return new Collection([]);
        }

        $results = $this->hydrate($response);

        wp_cache_set($cacheName, $results, 'metricool', MINUTE_IN_SECONDS);

        return $results;
    }

    /**
     * Creates a collection of \Metricool\Http\Metricool\Dto\Statistic objects for
     * each result of the response. Usage can be seen here:
     * {@see \Metricool\Services\AnalyticsService::getTotalAmount}
     * @return Collection<Statistic>
     */
    public function hydrate(array $response) : Collection
    {
        // todo: use Storage for Collections
        return new Collection(array_map(function($row) {
            return new Statistic($row[0], $row[1]);
        }, $response));
    }

    /**
     * Return the metric that the current instance is used for. Useful for
     * dynamic retrieval of the intent of the instance. For an example see:
     * {@see \Metricool\Services\AnalyticsService::getTrend}
     */
    public function getMetric(): string
    {
        return $this->metric;
    }
}