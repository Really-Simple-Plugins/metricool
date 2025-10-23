<?php

namespace Metricool\Http\Endpoints\Responses;

use Metricool\Builders\StatsChartTableBuilder;
use Metricool\Helpers\Collection;
use Metricool\Http\Metricool\DTOs\DistributionDTO;

/**
 * A DistributionResponse shows Table and Chart data. The TableData
 * will have a percentage of the total distribution of the metric. The result
 * in the Chart holds the values to be used inside the Chart
 *
 * These Responses are dynamically created from endpoints/DistributionEndpoint.
 * @see \Metricool\Http\Endpoints\DistributionEndpoint
 */
abstract class DistributionResponse extends Response
{
    /** @var Collection|DistributionDTO[] */
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
     * Leave empty to not include the chart data
     * @see \Metricool\Http\Endpoints\Responses\Statistics\CountriesResponse::getChartColumns()
     */
    protected function getChartColumns(): array
    {
        return [];
    }

    /**
     * @inheritDoc
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
     * Gets the chart to be used in response. If the columns are empty,
     * no chart will be produced
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
     * Calculates the total amount of the results
     * @param Collection|DistributionDTO[] $results
     */
    public function getTotalAmountOfResults(Collection $results): int
    {
        return $results->sum('amount');
    }

    /**
     * Processes results, this calculates distribution percentages on each result
     * and adds them to the results collection.
     * @param Collection|DistributionDTO[] $results
     */
    protected function processResults(Collection $results): self
    {
        $total = $this->getTotalAmountOfResults($results);

        foreach ($results as $result) {
            $this->results->push($this->getSingleItem($result, $total));
        }

        return $this;
    }

    /**
     * Mutate the DistributionDTO to the requirements of the endpoint and
     * calculates the distribution percentage
     */
    abstract protected function getSingleItem(DistributionDTO $item, int $total): object;
}