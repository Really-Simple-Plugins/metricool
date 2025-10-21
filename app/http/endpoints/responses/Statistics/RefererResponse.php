<?php

namespace Metricool\Http\Endpoints\responses\Statistics;

use Metricool\Http\Endpoints\responses\StatisticsResponse;
use Metricool\Http\Metricool\DTOs\DistributionDTO;

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

    public function getSingleItem(DistributionDTO $item, int $total): object
    {
        return (object)[
            'url' => $item->value,
            'pageViews' => $item->amount,
            'percentage' => $item->calculatePercentageFromTotal($total),
        ];
    }
}
