<?php

namespace Metricool\Http\Responses;

use Metricool\Helpers\Collection;
use Metricool\Builders\StatsChartTableBuilder;
use Metricool\Http\Metricool\DTOs\DistributionDTO;
use Metricool\Http\Metricool\DTOs\DTO;

/**
 * A DistributionResponse shows Table and Chart data. The TableData
 * will have a percentage of the total distribution of the metric. The result
 * in the Chart holds the values to be used inside the Chart
 *
 * These responses are dynamically created from endpoints/DistributionEndpoint.
 * @see \Metricool\Http\Endpoints\DistributionEndpoint
 */
abstract class StatisticsResponse extends Response
{
    /** @var Collection<DistributionDTO> */
    protected Collection $results;

    public function __construct(Collection $results)
    {
        $this->results = new Collection();

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
    public function getTotalAmountOfResults(Collection $results): int
    {
        return $results->sum('amount');
    }

    /**
     * Processes results, sets distribution percentages on each result
     */
    protected function processResults($results): self
    {
        $total = $this->getTotalAmountOfResults($results);

        foreach ($results as $result) {
            $this->results->push($this->getSingleItem($result, $total));
        }

        return $this;
    }

    /**
     * Mutate the DistributionDTO to the requirements of the endpoint
     */
    abstract protected function getSingleItem(DistributionDTO $item, int $total): object;
}