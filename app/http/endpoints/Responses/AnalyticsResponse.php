<?php

namespace Metricool\Http\Endpoints\Responses;

use Metricool\Services\Analytics\TrendService;
use Metricool\Services\AnalyticsService;

class AnalyticsResponse extends Response
{
    public AnalyticsService $service;

    public function __construct(array $requestFilters = [])
    {
        $this->service = (new AnalyticsService(new TrendService()))
            ->setRequestFilters($requestFilters);
    }

    public function body(): array
    {
        return [
            'totals' => $this->service->getTotals(),
            'timelineData' => $this->service->getTimelineData(),
        ];
    }
}