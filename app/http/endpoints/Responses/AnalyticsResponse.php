<?php

namespace Metricool\Http\Endpoints\Responses;

use Metricool\Http\Metricool\Entities\TimelineStatistics;
use Metricool\Services\Analytics\TrendService;
use Metricool\Services\AnalyticsService;

class AnalyticsResponse extends Response
{
    protected AnalyticsService $service;

    public function __construct(array $requestFilters = [])
    {
        $this->service = new AnalyticsService(new TrendService());

        if (isset($requestFilters['start'])) {
            $this->service->setStartDate($requestFilters['start']);
        }

        if (isset($requestFilters['end'])) {
            $this->service->setEndDate($requestFilters['end']);
        }
    }

    public function addMetric($name, $label, TimelineStatistics $statistics)
    {
        $this->service->loadMetric($name, $label, $statistics);
    }

    public function body(): array
    {
        return [
            'totals' => $this->service->getTotals(),
            'timelineData' => $this->service->getTimelineData(),
        ];
    }
}