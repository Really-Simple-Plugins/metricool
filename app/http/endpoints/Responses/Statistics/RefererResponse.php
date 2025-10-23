<?php

namespace Metricool\Http\Endpoints\Responses\Statistics;

use Metricool\Http\Endpoints\Responses\StatisticsResponse;
use Metricool\Http\Metricool\DTOs\DistributionDTO;

class RefererResponse extends StatisticsResponse
{
    /**
     * The columns for the country chart. Keys represent the property,
     * value is the label for this property
     * @see \Metricool\Builders\StatsChartTableBuilder::setColumns()
     */
    public function getChartColumns(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getSingleItem(DistributionDTO $item, int $total): object
    {
        return (object)[
            'url' => $item->value,
            'pageViews' => $item->amount,
            'percentage' => $item->calculatePercentageFromTotal($total),
        ];
    }
}
