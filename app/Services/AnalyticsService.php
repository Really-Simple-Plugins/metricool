<?php

declare(strict_types=1);

namespace Metricool\Services;

use InvalidArgumentException;
use Metricool\Support\Builders\StatsTimelineBuilder;
use Metricool\Support\Helpers\Collection;
use Metricool\Http\Metricool\DTOs\TimelineDTO;
use Metricool\Http\Metricool\Entities\TimelineStatistics;
use Metricool\Services\Analytics\TrendService;
use Metricool\Support\Utility\DateUtility;

class AnalyticsService
{
    protected TrendService $trendService;

    protected array $requestFilters = [];
    /**
     * Metrics holds the name of the Metric, TimelineStatistics and results of the API request
     * @var array<string, array{
     *     name: string,
     *     label: string,
     *     statistics: TimelineStatistics,
     *     results: Collection|TimelineDTO[],
     *     useInTimeline: bool,
     *  }>
     **/
    protected array $metrics = [];

    public function __construct(TrendService $trendService)
    {
        $this->trendService = $trendService;
    }

    public function setRequestFilters(array $requestFilters): self
    {
        $this->requestFilters = $requestFilters;

        return $this;
    }

    /**
     * Sets the metrics to be used in the analytics service
     * This will fetch the results from the API and store them
     */
    public function loadMetric(string $metric, string $label, TimelineStatistics $statistics): self
    {
        $this->metrics[$metric] = [
            'name' => $metric,
            'label' => $label,
            'statistics' => $statistics,
            'results' => $statistics->filter($this->requestFilters)->get(),
        ];

        return $this;
    }

    public function getTotals(): array
    {
        $totals = [];
        foreach ($this->metrics as $metric => $metricData) {
            $totals[$metric] = [
                'label' => $metricData['label'],
                'totalAmount' => $this->calcTotalAmount($metric),
                'trend' => $this->getTrend($metric),
            ];
        }
        return $totals;
    }

    /**
     * Gets the results of a metric
     * @return Collection<int, TimelineDTO>
     * @throws InvalidArgumentException
     */
    public function getResults(string $metric): Collection
    {
        if (array_key_exists($metric, $this->metrics) === false) {
            throw new InvalidArgumentException("Incompatible metric given: $metric");
        }

        return $this->metrics[$metric]['results'];
    }

    /**
     * Gets the TimelineStatistics Entity of a metric
     * @throws InvalidArgumentException
     */
    public function getTimelineStatistics(string $metric): TimelineStatistics
    {
        if (array_key_exists($metric, $this->metrics) === false) {
            throw new InvalidArgumentException("Incompatible metric given: $metric");
        }

        return $this->metrics[$metric]['statistics'];
    }

    /**
     * Sums the amount of hits of this metric
     */
    public function calcTotalAmount(string $metric): float
    {
        return $this->getResults($metric)
            ->sum('amount');
    }

    /**
     * Returns the trend on the previous period
     */
    public function getTrend(string $metric): string
    {
        return $this->trendService->getTrend(
            $this->getTimelineStatistics($metric),
            $this->getResults($metric),
            $this->requestFilters
        );
    }

    /**
     * Builds the timeline
     * @see \Metricool\Http\Endpoints\AnalyticsEndpoint
     */
    public function getTimelineData(): array
    {
        $builder = new StatsTimelineBuilder();

        // todo: Not sure if this ok. Because period filter is deep inside the TimelineStatistics class we cannot access it from here.
        // to fix it would require a complete rework of the filter system

        // Switch date format based on period filter
        switch ($this->requestFilters['period'] ?? null) {
            case 'yesterday':
                $builder->setDateFormat('LT');
                break;
            case 'lastweek':
                $builder->setDateFormat('ddd D MMM');
                break;
            case 'currentmonth':
            case 'lastmonth':
            case 'previousmonth':
            case 'last30days':
                $builder->setDateFormat('D MMM');
                break;
            default:
                $builder->setDateFormat(DateUtility::localIsoDateMonthFormat());
        }

        return $builder->setMetrics($this->metrics)
            ->build();
    }
}
