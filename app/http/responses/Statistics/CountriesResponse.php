<?php

namespace Metricool\Http\Responses\Statistics;

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
            'metric' => 'value',
            'country' => esc_html__('Country', 'metricool'),
            'visitors' => esc_html__('Visitors', 'metricool')
        ];
    }

}
