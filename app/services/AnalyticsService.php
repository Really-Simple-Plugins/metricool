<?php

namespace Metricool\Services;

use Carbon\Carbon;
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
    /** @var array<string, array{timelineStatistics: Collection<TimelineStatistic>, results: Collection<TimelineStatistic>}> */
    protected array $metrics = [];

    public function __construct(TrendService $trendService)
    {
        $this->trendService = $trendService;

        // set default start and end date
        $this->startDate = Carbon::now()->subDays(30);
        $this->endDate = Carbon::now();
    }

    public function setStartDate(string $date, string $format = 'Ymd'): self
    {
        $this->startDate = Carbon::createFromFormat($format, $date);

        return $this;
    }

    public function setEndDate(string $date, string $format = 'Ymd'): self
    {
        $this->endDate = Carbon::createFromFormat($format, $date);

        return $this;
    }

    /**
     * Sets the metrics to be used in the analytics service
     * @param string $metric
     * @param TimelineStatistics $statistics
     * @return self
     */
    public function loadMetric(string $metric, TimelineStatistics $statistics): self
    {
        try {
            $this->metrics[$metric] = [
                'timelineStatistics' => $statistics,
                'results' => $statistics->filter($this->getFilters())
                    ->get()
            ];
        } catch(\Throwable $e) {

        }

        return $this;
    }

    /**
     * Creates the filters to be used in the timeline statistics
     * @return array
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
     * @param string $metric
     * @return Collection<int, TimelineStatistic>
     */
    public function getResults(string $metric) : Collection
    {
        return $this->metrics[$metric]['results'];
    }

    /**
     * Gets the TimelineStatistics Entity of a metric
     * @param string $metric
     * @return TimelineStatistics
     */
    public function getTimelineStatistics(string $metric): TimelineStatistics
    {
        return $this->metrics[$metric]['timelineStatistics'];
    }

    /**
     * Sums the amount of hits of this metric
     * @param string $metric
     * @return float
     */
    public function getTotalAmount(string $metric): float
    {
        return $this->getResults($metric)
            ->sum('hits');
    }

    /**
     * Returns the trend on the previous period
     * @param string $metric
     * @return string
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
     * @return array
     */
    public function getTimelineData(): array
    {
        return (new TimelineResponseBuilder())->setMetrics($this->metrics)
            ->build();
    }
}

