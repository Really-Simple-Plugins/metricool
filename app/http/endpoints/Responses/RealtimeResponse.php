<?php

namespace Metricool\Http\Endpoints\Responses;

use Metricool\Helpers\Collection;
use Metricool\Http\Metricool\DTOs\TimelineDTO;
use Metricool\Services\RealtimeService;

class RealtimeResponse extends Response
{
    public RealtimeService $service;

    public function __construct()
    {
        $this->service = new RealtimeService();
    }

    public function addMetric(string $name, string $label, array $results, bool $useInTimeline = true): self
    {
        $results = $this->hydrateResults($this->orderResults($results));

        $this->service->loadMetric($name, $label, $results, $useInTimeline);

        return $this;
    }

    protected function orderResults($results)
    {
        ksort($results);
        return $results;
    }

    protected function hydrateResults(array $results): Collection
    {
        $timeline = new Collection();

        foreach ($results as $timestamp => $amount) {
            $timeline->push(new TimelineDTO($timestamp, $amount));
        }

        return $timeline;
    }

    public function body(): array
    {
        return [
            'totals' => $this->service->getTotals(),
            'timelineData' => $this->service->getTimelineData(),
        ];
    }
}