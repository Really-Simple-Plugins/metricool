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