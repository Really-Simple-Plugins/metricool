<?php

namespace Metricool\Builders;

use Metricool\Helpers\Collection;
use Metricool\Http\Metricool\DTOs\DistributionStatistic;
use Metricool\Http\Metricool\DTOs\DTO;

/**
 * Builds an array that creates the data for charts.
 * Example:
 * [
 *   [
 *     "value", "Country", "Visitors"
 *   ],
 *   [
 *     "nl", "Netherlands", "121321300"
 *   ],
 *   [
 *     "ar", "Argentina", "22342"
 *   ]
 * ]
 * @see DistributionStatisticsService::getChartsData() for usage example
 */
class StatsChartTableBuilder
{
    /** @var Collection<DTO>  */
    private Collection $results;
    private array $columns;

    /**
     * Sets the columns that holds the property names of the DTO to be used in the chart table.
     * Example:
     * [
     *   'amount' => esc_html__('Amount', 'metricool'),
     *   'metric' => esc_html__('Visitors', 'metricool')
     * ]
     */
    public function setColumns(array $columns): self
    {
        $this->columns = $columns;

        return $this;
    }

    /**
     * Sets the results from the DistributionStatistics Entity
     * @param Collection<DistributionStatistic> $results
     */
    public function setResults(Collection $results): self
    {
        $this->results = $results;

        return $this;
    }

    /**
     * Build the chart data
     */
    public function build(): array
    {
        $chartTable = [];

        $chartTable[] = $this->getColumnLabels();

        foreach ($this->results as $result) {
            $chartTable[] = $this->createRow($result);
        }

        return $chartTable;
    }

    /**
     * Returns the row that holds the column labels
     */
    protected function getColumnLabels(): array
    {
        return array_values($this->columns);
    }

    /**
     * Creates a row into the chart based on the chartColumns
     * Each key of the chart column is a property of the DTO
     */
    protected function createRow(DTO $result): array
    {
        $row = [];

        foreach ($this->columns as $property => $column) {
            $row[] = $result->{$property};
        }

        return $row;
    }
}
