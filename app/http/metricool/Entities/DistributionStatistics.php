<?php

namespace Metricool\Http\Metricool\Entities;

use Carbon\Carbon;
use Metricool\Helpers\Collection;
use Metricool\Http\Metricool\DTOs\DistributionStatistics\CountryDTO;
use Metricool\Http\Metricool\DTOs\DistributionStatistics\ReferrerDTO;
use Metricool\Http\Metricool\MetricoolClient;
use Metricool\Http\Metricool\Traits\isFilterable;
use Metricool\Traits\isHydratable;

/**
 * API responses for distribution statistics include data on how various metrics
 * are distributed. Such as page views by country, referrer pages, or traffic
 * sources.
 */
class DistributionStatistics
{
    use isFilterable;
    use isHydratable;

    protected MetricoolClient $client;
    protected string $endpoint = 'stats/distribution/';
    protected string $metric;

    /**
     * The distribution statistics API is compatible with these metrics.
     */
    private array $metrics = [
        'country' => CountryDTO::class,
        'referers' => ReferrerDTO::class,
        //'sources',
    ];

    /**
     * Pass a compatible metric to the constructor: {@see metrics}
     * @throws \InvalidArgumentException
     */
    public function __construct(MetricoolClient $client, string $metric, bool $filterRequired = true)
    {
        if (!array_key_exists($metric, $this->metrics)) {
            throw new \InvalidArgumentException("Incompatible metric given: $metric");
        }

        $this->metric = $metric;
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
     * Hydrate every result into a DistributionStatisticDTO object
     * @return mixed
     */
    protected function hydrateItem($key, $item): object
    {
        return new $this->metrics[$this->metric]($this->metric, $key, $item, 0);
    }

    /**
     * Fetch and return the distribution statistics data
     */
    public function get(): Collection
    {
        if ($this->requiresFilter && $this->filtered === false) {
            $this->filter($this->filters);
        }

        return $this->hydrateResults($this->client->get($this->endpoint));
    }

}