<?php

namespace Metricool\Http\Endpoints\Responses;

use Metricool\Services\RealtimeService;

class RealtimeResponse extends Response
{
    public RealtimeService $service;

    public function __construct()
    {
        $this->service = new RealtimeService();
    }

    /**
     * Adds a metric to the service. The results will be hydrated and ordered before being added to the service.
     * @param $results array{timestamp: int, amount: float} Results from a Metricool timeline response
     */
    public function addMetric(string $name, string $label, array $results, bool $useInTimeline = true): self
    {
        // create an array of TimelineDTO's and sort them
        $results = $this->hydrateTimelineResults($results)->sortBy('timestamp');

        // add the sorted results to the service
        $this->service->addMetric($name, $label, $results, $useInTimeline);

        return $this;
    }


    /**
     * Creates the response body
     */
    public function body(): array
    {
        return [
            'totals' => $this->service->getTotals(),
            'timelineData' => $this->service->getTimelineData(),
        ];
    }
}