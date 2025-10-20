<?php

namespace Metricool\Builders;

use Carbon\Carbon;
use Metricool\Helpers\Collection;
use Metricool\Http\Metricool\DTOs\TimelineStatistics\TimelineDTO;

/**
 * Builds a timeline from a collection of metrics and their corresponding statistics.
 * @see AnalyticsService for usage example
 */
class TimelineResponseBuilder
{
    public array $timeline = [];
    /** @var array<string, array{timelineStatistics: Collection<TimelineDTO>, results: Collection<TimelineDTO>}> */
    protected array $metrics = [];

    /**
     * Combines statistics within the same timestamp data into a timeline.
     * Useful for the dashboard charts.
     */
    public function build(): array
    {
        foreach ($this->metrics as $name => $metric) {
            $statistics = ($metric['results'] ?? []);
            foreach ($statistics as $statistic) {
                if ($this->hasRow($statistic->timestamp) === false) {
                    $this->createRow($statistic->timestamp);
                }

                $this->addMetricToRow($this->timeline[$statistic->timestamp], $name, $statistic);
            }
        }

        return $this->getTimelineRows();
    }

    /**
     * Sets the metrics that should be included in a timeline item
     * @param array<string, array{timelineStatistics: Collection<TimelineDTO>, results: Collection<TimelineDTO>}> $metrics
     */
    public function setMetrics(array $metrics): self
    {
        $this->metrics = $metrics;

        return $this;
    }

    /**
     * Returns the timeline without preserving keys.
     */
    public function getTimelineRows(): array
    {
        return array_values($this->timeline);
    }

    /**
     * Returns a row on the given timestamp
     */
    protected function getRow(int $datestamp): ?array
    {
        return $this->timeline[$datestamp] ?? null;
    }

    /**
     * Checks if a row exists on the given timestamp
     */
    protected function hasRow($datestamp) : bool
    {
        return ($this->getRow($datestamp) !== null);
    }

    /**
     * Creates a row for the given timestamp. Each key in the metrics is a property.
     * This uses the $metrics to create a row which contains a property for
     * every metric with an initial value of 0.
     */
    protected function createRow(int $timestamp): array
    {
        $row = [
            'timestamp' => $timestamp,
            'date' => Carbon::createFromTimestampMs($timestamp)->format('j M'),
        ];

        // initialize the properties for each metric, these are the keys of the metrics
        foreach (array_keys($this->metrics) as $metric) {
            $row[$metric] = 0.0;
        }

        return $this->addRowToTimeline($timestamp,$row);
    }

    /**
     * Inserts a row to the timeline
     */
    protected function addRowToTimeline($timestamp, $row): array
    {
        $this->timeline[$timestamp] = $row;

        return $this->timeline[$timestamp];
    }

    /**
     * Adds a metric (Visits / PageViews / etc) to the row
     */
    protected function addMetricToRow(&$row, string $metric, TimelineDTO $statistic): void
    {
        $row[$metric] = $statistic->amount;
    }
}
