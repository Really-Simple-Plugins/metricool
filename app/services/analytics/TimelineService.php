<?php

namespace Metricool\Services\Analytics;

use Carbon\Carbon;
use Metricool\Http\Metricool\DTO\Statistic;
use Metricool\Http\Metricool\Entities\TimelineStatistics;

class TimelineService
{
    public array $timeline = [];
    protected array $metrics = [];

    public function setMetrics(array $metrics): self
    {
        $this->metrics = $metrics;

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
        foreach ($this->metrics as $name => $metric) {
            $statistics = $metric['results'];
            foreach ($statistics as $statistic) {
                if ($this->hasRow($statistic->timestamp) === false) {
                    $this->createRow($statistic->timestamp);
                }

                $this->addMetricToRow($this->timeline[$statistic->timestamp], $name, $statistic);
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
        foreach (array_keys($this->metrics) as $metric) {
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
