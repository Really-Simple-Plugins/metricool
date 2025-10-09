<?php

namespace Metricool\Services\Analytics;

use Carbon\Carbon;
use Metricool\Http\Metricool\DTO\Statistic;
use Metricool\Http\Metricool\Entities\TimelineStatistics;

class TimelineService
{
    public array $timeline = [];

    /**
     * Combines statistics within the same timestamp data into a timeline.
     * Useful for the dashboard charts.
     * @param TimelineStatistics[] $timelineStatistics
     * @return array
     */
    public function createTimeline(array $timelineStatistics) : array
    {
        foreach ($timelineStatistics as $metric => $timelineStatistic) {
            $statistics = $timelineStatistic->get();
            foreach ($statistics as $statistic) {
                if (!$this->getRow($statistic->timestamp)) {
                    $this->createRow($statistic->timestamp, $timelineStatistics);
                }

                $this->addMetricToRow($this->timeline[$statistic->timestamp], $metric, $statistic);
            }
        }

        return $this->getTimeline();
    }

    public function getTimeline() : array
    {
        return array_values($this->timeline);
    }

    protected function getRow($datestamp)
    {
        return $this->timeline[$datestamp] ?? null;
    }

    public function createRow($timestamp, $timelineStatistics)
    {
        $row = [
            'timestamp' => $timestamp,
            'date' => Carbon::createFromTimestamp($timestamp / 1000)->format('j M'), // todo: fix magic number
        ];

        // initialize properties for each metric
        foreach ($timelineStatistics as $metric => $timelineStatistic) {
            $row[$metric] = 0.0;
        }

        return $this->addRowToTimeline($timestamp,$row);
    }

    protected function addRowToTimeline($timestamp, $row)
    {
        $this->timeline[$timestamp] = $row;

        return $this->timeline[$timestamp];
    }

    public function addMetricToRow(&$row, string $metric, Statistic $statistic) : void
    {
        $row[$metric] = $statistic->getValue();
    }
}
