<?php

namespace Metricool\Http\Responses\Statistics;

use Locale;
use Metricool\Http\Metricool\DTOs\DistributionDTO;
use Metricool\Http\Responses\StatisticsResponse;

class CountriesResponse extends StatisticsResponse
{
    /**
     * The columns for the country chart
     * @see \Metricool\Builders\StatsChartTableBuilder::setColumns()
     */
    public function getChartColumns(): array
    {
        return [
            'value' => 'value',
            'country' => esc_html__('Country', 'metricool'),
            'visitors' => esc_html__('Visitors', 'metricool'),
        ];
    }

    protected function getSingleItem(DistributionDTO $item, int $total): object
    {
        return (object) [
            'value' => $item->value,
            'country' => Locale::getDisplayRegion('-' . $item->value, get_user_locale()),
            'visitors' => $item->amount,
            'percentage' => $item->calculatePercentageFromTotal($total),
        ];
    }
}
