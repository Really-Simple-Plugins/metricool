<?php

namespace Metricool\Http\Responses\Statistics;

use Metricool\Helpers\Collection;
use Metricool\Http\Metricool\DTOs\DistributionStatistics\ReferrerDTO;
use Metricool\Http\Responses\StatisticsResponse;

class RefererResponse extends StatisticsResponse
{
    /**
     * The columns for the referrer chart
     * @see \Metricool\Builders\StatsChartTableBuilder::setColumns()
     */
    public function getChartColumns(): array
    {
        return [];
    }
}
