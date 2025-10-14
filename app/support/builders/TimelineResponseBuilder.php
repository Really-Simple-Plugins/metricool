<?php

namespace Metricool\Builders;

use Carbon\Carbon;
use Metricool\Helpers\Collection;
use Metricool\Http\Metricool\DTO\Statistic;

class TimelineResponseBuilder
{
    public array $timeline = [];
    /** @var array<string, array{timelineStatistics: Collection<Statistic>, results: Collection<Statistic>}> */
    protected array $metrics = [];

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

    /**
     * Sets the metrics that should be included in a timeline item
     * @param array<string, array{timelineStatistics: Collection<Statistic>, results: Collection<Statistic>}> $metrics
     * @return self
     */
    public function setMetrics(array $metrics): self
    {
        $this->metrics = $metrics;

        return $this;
    }

    /**
     * Returns the timeline without preserving keys.
     * @return array
     */
    public function getTimeline(): array
    {
        return array_values($this->timeline);
    }

    /**
     * Returns a row on the given timestamp
     * @param int $datestamp
     * @return array|null
     */
    protected function getRow(int $datestamp): ?array
    {
        return $this->timeline[$datestamp] ?? null;
    }

    /**
     * Checks if a row exists on the given timestamp
     * @param $datestamp
     * @return bool
     */
    protected function hasRow($datestamp) : bool
    {
        return ($this->getRow($datestamp) !== null);
    }

    /**
     * Creates a row for the given timestamp
     * @param int $timestamp
     * @return array
     */
    protected function createRow(int $timestamp): array
    {
        $timestampInSeconds = $timestamp / 1000;

        $row = [
            'timestamp' => $timestamp,
            'date' => Carbon::createFromTimestamp($timestampInSeconds)->format('j M'),
        ];

        // initialize properties for each metric
        foreach (array_keys($this->metrics) as $metric) {
            $row[$metric] = 0.0;
        }

        return $this->addRowToTimeline($timestamp,$row);
    }

    /**
     * Inserts a row to the timeline
     * @param $timestamp
     * @param $row
     * @return array
     */
    protected function addRowToTimeline($timestamp, $row): array
    {
        $this->timeline[$timestamp] = $row;

        return $this->timeline[$timestamp];
    }

    /**
     * Adds a metric (Visits / PageViews / etc) to the row
     * @param $row
     * @param string $metric
     * @param Statistic $statistic
     */
    protected function addMetricToRow(&$row, string $metric, Statistic $statistic): void
    {
        $row[$metric] = $statistic->getHits();
    }
}
