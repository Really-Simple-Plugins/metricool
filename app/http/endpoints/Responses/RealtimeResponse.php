<?php

namespace Metricool\Http\Endpoints\Responses;


class RealtimeResponse extends Response
{
    public array $totals;
    public array $timelineData;

    public function setTotals(array $totals): self
    {
        $this->totals = $totals;

        return $this;
    }

    public function setTimelineData($timeline): self
    {
        $this->timelineData = $timeline;

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