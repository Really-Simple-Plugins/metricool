<?php

namespace Metricool\Http\Endpoints\Responses\Statistics;

use Locale;
use Metricool\Http\Endpoints\Responses\DistributionResponse;
use Metricool\Http\Metricool\DTOs\DistributionDTO;

class CountriesResponse extends DistributionResponse
{
    /**
     * The columns for the country chart. Keys represent the property,
     * value is the label for this property
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

    /**
     * @inheritDoc
     */
    protected function getSingleItem(DistributionDTO $item, int $total): object
    {
        return (object)[
            'value' => $item->value,
            'country' => Locale::getDisplayRegion('-' . $item->value, get_user_locale()),
            'visitors' => $item->amount,
            'percentage' => $item->calculatePercentageFromTotal($total),
        ];
    }
}
