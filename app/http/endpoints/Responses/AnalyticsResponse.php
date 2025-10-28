<?php

namespace Metricool\Http\Endpoints\Responses;

class AnalyticsResponse extends Response
{
    /**
     * Sets the totals to be shown in the response
     * @see \Metricool\Services\AnalyticsService::getTotals()
     */
    public array $totals = [];
    /**
     * Sets the totals to be shown in the response
     * @see \Metricool\Services\AnalyticsService::getTimelineData()
     */
    public array $timelineData = [];

    public function setTotals($totals): self
    {
        $this->totals = $totals;

        return $this;
    }

    public function setTimelineData($timelineData): self
    {
        $this->timelineData = $timelineData;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function body(): array
    {
        return [
            'totals' => $this->totals,
            'timelineData' => $this->timelineData,
        ];
    }
}