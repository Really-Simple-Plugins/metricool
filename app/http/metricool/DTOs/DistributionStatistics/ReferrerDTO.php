<?php

namespace Metricool\Http\Metricool\DTOs\DistributionStatistics;

use Metricool\Http\Metricool\DTOs\DistributionStatistic;

class ReferrerDTO extends DistributionStatistic
{
    /**
     * Return the serialized version
     */
    public function toArray(): array
    {
        return [
            'url' => $this->metric,
            'pageViews' => $this->amount,
            'percentage' => $this->percentage
        ];
    }
}