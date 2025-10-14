<?php

namespace Metricool\Services;

use Carbon\Carbon;
use Metricool\Helpers\Collection;
use Metricool\Http\Metricool\Dto\Statistic;
use Metricool\Services\Analytics\TimelineService;
use Metricool\Services\Analytics\TrendService;
use Metricool\Http\Metricool\Entities\TimelineStatistics;

class AnalyticsService
{
    protected Carbon $startDate;
    protected Carbon $endDate;

    protected TrendService $trendService;
    protected TimelineService $timelineService;
    /** @var array<string, array{timelineStatistics: Collection<int, Statistic>, results: Collection<int, Statistic>}> */
    protected array $metrics = [];

    public function __construct(TrendService $trendService, TimelineService $timelineService)
    {
        $this->trendService = $trendService;
        $this->timelineService = $timelineService;

        // set default start and end date
        $this->startDate = Carbon::now();
        $this->endDate = Carbon::now()->subDays(30);
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
     * @return Collection<int, Statistic>
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
    public function createTimeline(): array
    {
        return $this->timelineService->setMetrics($this->metrics)
            ->build();
    }
}

