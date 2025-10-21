<?php

namespace Metricool\Http\Responses\Statistics;

use Metricool\Http\Metricool\DTOs\DistributionDTO;
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

    public function getSingleItem(DistributionDTO $item, int $total): object
    {
        return (object) [
            'url' => $item->value,
            'pageViews' => $item->amount,
            'percentage' => $item->calculatePercentageFromTotal($total)
        ];
    }
}
