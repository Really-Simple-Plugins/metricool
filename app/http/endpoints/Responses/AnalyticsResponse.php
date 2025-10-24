<?php

namespace Metricool\Http\Endpoints\Responses;

class AnalyticsResponse extends Response
{
    public array $totals;
    public array $timelineData;

    /**
     * Sets the totals to be shown in the response
     * [''
     * ]
     */
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