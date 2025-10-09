<?php

namespace Metricool\Services;

use Carbon\Carbon;
use Metricool\Services\Analytics\TimelineService;
use Metricool\Services\Analytics\TrendService;
use Metricool\Http\Metricool\Entities\TimelineStatistics;

class AnalyticsService
{
    protected Carbon $startDate;
    protected Carbon $endDate;

    protected TrendService $trendService;
    protected TimelineService $timelineService;
    /** @var TimelineStatistics[] $statistics */
    protected array $statistics = [];

    public function __construct()
    {
        // dependency injection?
        $this->trendService = new TrendService();
        $this->timelineService = new TimelineService();
    }

    public function setStartDate(Carbon $startDate) : self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function setEndDate(Carbon $endDate) : self
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * Sets the metrics to be used in the analytics service
     * @param string $name
     * @param TimelineStatistics $statistics
     * @return self
     */
    public function addStatistic(string $name, TimelineStatistics $statistics) : self
    {
        $this->statistics[$name] = $statistics
            ->filter(['start' => $this->startDate, 'end' => $this->endDate]);

        return $this;
    }

    /**
     * Gets all metrics
     * @return TimelineStatistics[] $statistics
     */
    public function getStatistics() : array
    {
        return $this->statistics;
    }

    /**
     * Gets a metric
     * @return TimelineStatistics
     */
    public function getStatistic(string $metric) : TimelineStatistics
    {
        return $this->statistics[$metric];
    }

    public function getTotalAmount(string $metric) : float // float??
    {
        return $this->getStatistic($metric)
            ->get()
            ->sum('value');
    }

    public function getTrend(string $metric) : string
    {
        return $this->trendService->getTrend($this->getStatistic($metric));
    }

    /**
     * Creates the timeline
     */
    public function createTimeline() : array
    {
        return $this->timelineService->createTimeline($this->statistics);
    }
}

