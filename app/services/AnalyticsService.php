<?php

namespace Metricool\Services;

use Carbon\Carbon;
use InvalidArgumentException;
use Metricool\Helpers\Collection;
use Metricool\Http\Metricool\Dto\TimelineStatistic;
use Metricool\Builders\TimelineResponseBuilder;
use Metricool\Services\Analytics\TrendService;
use Metricool\Http\Metricool\Entities\TimelineStatistics;

class AnalyticsService
{
    protected Carbon $startDate;
    protected Carbon $endDate;

    protected TrendService $trendService;
    /**
     * Metrics holds the name of the Metric, TimelineStatistics and results of the API request
     * @var array<string, array{
     *     name: string,
     *     timelineStatistics: Collection<TimelineStatistic>,
     *     results: Collection<TimelineStatistic>
     *  }>
     **/
    protected array $metrics = [];

    public function __construct(TrendService $trendService)
    {
        $this->trendService = $trendService;

        // set default start and end date
        $this->startDate = Carbon::now()->subDays(30);
        $this->endDate = Carbon::now();
    }

    /**
     * Sets the startDate for the metrics. Overrides the default startDate
     * @param string $date The date string
     * @param string $format The date format of the given date
     */
    public function setStartDate(string $date, string $format = 'Ymd'): self
    {
        $this->startDate = Carbon::createFromFormat($format, $date);

        return $this;
    }

    /**
     * Sets the endDate for the metrics. Overrides the default endDate
     * @param string $date The date string
     * @param string $format The date format of the given date
     */
    public function setEndDate(string $date, string $format = 'Ymd'): self
    {
        $this->endDate = Carbon::createFromFormat($format, $date);

        return $this;
    }

    /**
     * Sets the metrics to be used in the analytics service
     * This will fetch the results from the API and store them
     */
    public function loadMetric(string $metric, TimelineStatistics $statistics): self
    {
        $this->metrics[$metric] = [
            'name' => $metric,
            'timelineStatistics' => $statistics,
            'results' => $statistics->filter($this->getFilters())
                ->get()
        ];

        return $this;
    }

    /**
     * Creates the filters to be used in the timeline statistics
     */
    public function getFilters(): array
    {
        return [
            'start' => $this->startDate->format('Ymd'),
            'end' => $this->endDate->format('Ymd')
        ];
    }

    /**
     * Gets the results of a metric
     * @return Collection<int, TimelineStatistic>
     * @throws InvalidArgumentException
     */
    public function getResults(string $metric) : Collection
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

        return $this->metrics[$metric]['timelineStatistics'];
    }

    /**
     * Sums the amount of hits of this metric
     */
    public function getTotalAmount(string $metric): float
    {
        return $this->getResults($metric)
            ->sum('hits');
    }

    /**
     * Returns the trend on the previous period
     */
    public function getTrend(string $metric): string
    {
        return $this->trendService->getTrend(
            $this->getTimelineStatistics($metric),
            $this->getResults($metric),
            $this->getFilters()
        );
    }

    /**
     * Builds the timeline
     * @see \Metricool\Http\Endpoints\AnalyticsEndpoint
     */
    public function getTimelineData(): array
    {
        return (new TimelineResponseBuilder())->setMetrics($this->metrics)
            ->build();
    }
}

