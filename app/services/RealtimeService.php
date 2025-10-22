<?php

namespace Metricool\Services;

use Metricool\Builders\StatsTimelineBuilder;
use Metricool\Helpers\Collection;
use Metricool\Http\Metricool\DTOs\TimelineDTO;

class RealtimeService
{
    /**
     * @var array<string, array{
     *     name: string,
     *     label: string,
     *     results: Collection<TimelineDTO>,
     *     useInTimeline: bool,
     *  }> Metrics holds the name, label and results of the metric
     **/
    protected array $metrics = [];

    /**
     * Sets the metrics to be used in the realtime service. The metrics contains the name, label and results of each metric.
     * @param Collection<TimelineDTO> $results
     */
    public function loadMetric(string $metric, string $label, Collection $results, $useInTimeline = true): self
    {
        $this->metrics[$metric] = [
            'name' => $metric,
            'label' => $label,
            'results' => $results,
            'useInTimeline' => $useInTimeline,
        ];

        return $this;
    }

    /**
     * Gets the results of a metric
     * @return Collection<int, TimelineDTO>
     * @throws \InvalidArgumentException
     */
    protected function getResults(string $metric): Collection
    {
        if (array_key_exists($metric, $this->metrics) === false) {
            throw new \InvalidArgumentException("Incompatible metric given: $metric");
        }

        return $this->metrics[$metric]['results'];
    }

    /**
     * Sums the amount of hits of this metric
     */
    protected function calcTotalAmount(string $metric): float
    {
        return $this->getResults($metric)
            ->sum('amount');
    }

    /**
     * Gets the totals to be used in the response
     */
    public function getTotals(): array
    {
        $totals = [];
        foreach ($this->metrics as $metric => $metricData) {
            $totals[$metric] = [
                'label' => $metricData['label'],
                'totalAmount' => $this->calcTotalAmount($metric),
            ];
        }
        return $totals;
    }

    /**
     * Builds the timeline
     * @see \Metricool\Http\Endpoints\AnalyticsEndpoint
     */
    public function getTimelineData(): array
    {
        return (new StatsTimelineBuilder())->setDateFormat('j M H:i')
            ->setMetrics($this->metrics)
            ->build();
    }
}