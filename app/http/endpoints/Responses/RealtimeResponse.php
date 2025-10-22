<?php

namespace Metricool\Http\Endpoints\Responses;

use Metricool\Helpers\Collection;
use Metricool\Http\Metricool\DTOs\TimelineDTO;
use Metricool\Services\RealtimeService;

class RealtimeResponse extends Response
{
    protected RealtimeService $service;

    public function __construct()
    {
        $this->service = new RealtimeService();
    }

    public function addMetric(string $name, string $label, array $results): self
    {
        $this->service->loadMetric($name, $label, $this->hydrateResults($results));

        return $this;
    }

    protected function hydrateResults(array $results): Collection
    {
        $timeline = new Collection();

        // order the results
        ksort($results);

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