<?php

namespace Metricool\Http\Metricool\Entities;

use GuzzleHttp\Exception\GuzzleException;
use Metricool\Helpers\Collection;
use Metricool\Http\Metricool\Dto\TimelineStatisticDTO;
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
     * @inheritDoc
     */
    protected function hydrateItem($key, $item): TimelineStatisticDTO
    {
        return new TimelineStatisticDTO($item[0], $item[1]);
    }

    /**
     * Fetch and return the timeline statistics data plainly from the API.
     * @return Collection<TimelineStatisticDTO>
     * @throws GuzzleException
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

        if ($this->isEmptyResponse($results)) {
            return new Collection([]);
        }

        if ($this->shouldHydrate) {
            $results = $this->hydrateResults($results);
        }

        wp_cache_set($cacheName, $results, 'metricool', MINUTE_IN_SECONDS);

        return $results;
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

    /**
     * When this endpoint holds no data, Metricool returns a result with
     * non-standard output. Just return an empty response when only 1 row is
     * found in the results and the value is 0
     */
    protected function isEmptyResponse($response): bool
    {
        return empty($response) || (is_array($response) && count($response) === 1 && empty($response[0][1]));
    }
}