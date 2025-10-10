<?php

namespace Metricool\Services;

use Carbon\Carbon;
use Illuminate\Support\Collection;
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

    public function setStartDate(string $date, string $format = 'dmY'): self
    {
        $this->startDate = Carbon::createFromFormat($date, $format);

        return $this;
    }

    public function setEndDate(string $date, string $format = 'dmY'): self
    {
        $this->endDate = Carbon::createFromFormat($date, $format);

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
        $this->metrics[$metric] = [
            'timelineStatistics' => $statistics,
            'results' => $statistics->filter([
                    'start' => $this->startDate,
                    'end' => $this->endDate
                ])
                ->get()
        ];

        return $this;
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

    public function getTotalAmount(string $metric): float // float??
    {
        return $this->getResults($metric)
            ->sum('value');
    }

    public function getTrend(string $metric): string
    {
        return $this->trendService->getTrend($this->getTimelineStatistics($metric), $this->getResults($metric));
    }

    /**
     * Creates the timeline
     */
    public function createTimeline(): array
    {
        return $this->timelineService->setMetrics($this->metrics)
            ->build();
    }
}

