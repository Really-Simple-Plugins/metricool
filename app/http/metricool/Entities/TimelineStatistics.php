<?php

namespace Metricool\Http\Metricool\Entities;

use Metricool\Helpers\Collection;
use Metricool\Http\Metricool\Dto\Statistic;
use Metricool\Http\Metricool\MetricoolClient;
use Metricool\Http\Metricool\Traits\isFilterable;
use Metricool\Traits\isHydratable;

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
    use isHydratable;

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
     * @return Collection<Statistic>
     * @throws \Exception
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function get(): Collection
    {
        if ($this->requiresFilter && $this->filtered === false) {
            if(empty($this->filters['start']) || empty($this->filters['end'])) {
                throw new \Exception('Start and end date are required for this timeline statistic');
            }
            $this->filter($this->filters);
        }

        $cacheName = 'timeline_statistics_' . $this->endpoint;
        if ($cache = wp_cache_get($cacheName, 'metricool')) {
            return $cache;
        }

        $results = $this->client->get($this->endpoint);

        /**
         * When this endpoint holds no data, Metricool returns a result with
         * non-standard output. Just return an empty response when only 1 row is
         * found in the results
         */
        if (is_array($results) && count($results) == 1) {
            $results = [];
        }

        if ($this->shouldHydrate) {
            $results = $this->hydrate($results);
        }

        $results = new Collection($results);

        wp_cache_set($cacheName, $results, 'metricool', MINUTE_IN_SECONDS);

        return $results;
    }

    protected function hydrateItem($item): Statistic
    {
        return new Statistic($item[0], $item[1]);
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