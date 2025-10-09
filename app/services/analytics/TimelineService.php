<?php

namespace Metricool\Services\Analytics;

use Carbon\Carbon;
use Metricool\Http\Metricool\DTO\Statistic;
use Metricool\Http\Metricool\Entities\TimelineStatistics;

class TimelineService
{
    public array $timeline = [];
    protected array $statistics = [];

    public function setStatistics(array $statistics): self
    {
        $this->statistics = $statistics;

        return $this;
    }

    /**
     * Combines statistics within the same timestamp data into a timeline.
     * Useful for the dashboard charts.
     *
     * @return array
     */
    public function build(): array
    {
        foreach ($this->statistics as $metric => $timelineStatistic) {
            $statistics = $timelineStatistic->get();
            foreach ($statistics as $statistic) {
                if (!$this->hasRow($statistic->timestamp)) {
                    $this->createRow($statistic->timestamp);
                }

                $this->addMetricToRow($this->timeline[$statistic->timestamp], $metric, $statistic);
            }
        }

        return $this->getTimeline();
    }

    public function getTimeline(): array
    {
        return array_values($this->timeline);
    }

    protected function getRow($datestamp): ?array
    {
        return $this->timeline[$datestamp] ?? null;
    }

    protected function hasRow($datestamp) : bool
    {
        return ($this->getRow($datestamp) !== null);
    }

    protected function createRow($timestamp): array
    {
        $row = [
            'timestamp' => $timestamp,
            'date' => Carbon::createFromTimestamp($timestamp / 1000)->format('j M'), // todo: fix magic number
        ];

        // initialize properties for each metric
        foreach ($this->statistics as $metric => $timelineStatistic) {
            $row[$metric] = 0.0;
        }

        return $this->addRowToTimeline($timestamp,$row);
    }

    protected function addRowToTimeline($timestamp, $row): array
    {
        $this->timeline[$timestamp] = $row;

        return $this->timeline[$timestamp];
    }

    protected function addMetricToRow(&$row, string $metric, Statistic $statistic): void
    {
        $row[$metric] = $statistic->getValue();
    }
}
