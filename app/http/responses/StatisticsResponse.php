<?php

namespace Metricool\Http\Responses;

use Metricool\Helpers\Collection;
use Metricool\Builders\StatsChartTableBuilder;

/**
 * A DistributionResponse shows Table and Chart data. The TableData
 * will have a percentage of the total distribution of the metric. The result
 * in the Chart holds the values to be used inside the Chart
 *
 * These responses are dynamically created from endpoints/StatisticsEndpoint.
 * @see \Metricool\Http\Endpoints\StatisticsEndpoint
 */
abstract class StatisticsResponse extends Response
{
    protected Collection $results;

    public function __construct(Collection $results)
    {
        // process and add the results, calculates the distribution
        $this->processResults($results);
    }

    /**
     * Gets the properties and labels to be used in the chart and
     * returns the column headers for the chart. Each property is assigned a label.
     * Example: ['property' => 'label']
     * @see \Metricool\Http\Responses\Statistics\CountriesResponse::getChartColumns()
     */
    abstract function getChartColumns(): array;


    /**
     * Creates the response body
     */
    public function body(): array
    {
        $response = [];
        $response['tableData'] = $this->getResultData();
        $response['chartDate'] = $this->getChartData();

        return $response;
    }

    /**
     * Gets the results to be used in response
     */
    public function getResultData(): array
    {
        return $this->results->toArray();
    }

    /**
     * Gets the chart to be used in response
     */
    public function getChartData(): array
    {
        $columns = $this->getChartColumns();

        if (empty($columns)) {
            return [];
        }

        return (new StatsChartTableBuilder())->setColumns($columns)
            ->setResults($this->results)
            ->build();
    }

    /**
     *  Calculates the total amount of the results
     */
    public function getTotalAmountOfResults(): int
    {
        return $this->results->sum('amount');
    }

    /**
     * Processes results, sets distribution percentages on each result
     */
    protected function processResults(Collection $results): self
    {
        $this->results = $results;
        $total = $this->getTotalAmountOfResults();

        foreach ($this->results as &$result) {
            $result->percentage = $this->calculateDistributionPercentage($result->amount, $total);
        }

        return $this;
    }

    /**
     * Calculate the distribution percentage
     */
    protected function calculateDistributionPercentage(int $amount, int $total): float
    {
        if($total === 0 || $amount === 0) {
            return 0;
        }
        return round((float) (($amount / $total) * 100), 3, PHP_ROUND_HALF_UP);
    }
}