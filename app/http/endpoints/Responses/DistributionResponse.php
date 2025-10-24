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
    /**
     * A collection of DistributionDTO's that holds the statistics of the
     * Metricool\Http\Metricool\Entities\DistributionStatistics Entity.
     * @var Collection|DistributionDTO[]
     */
    protected Collection $statistics;

    public string $sort = 'percentage';
    public string $order = 'desc';

    public function __construct(Collection $statistics)
    {
        $this->statistics = $statistics;
    }

    /**
     * @inheritDoc
     */
    public function body(): array
    {
        $results = $this->parse()->sortBy($this->sort, $this->order === 'desc');

        $response = [];
        $response['tableData'] = $this->createTableData($results);
        $response['chartData'] = $this->createChartData($results);

        return $response;
    }

    /**
     * Processes results, this calculates distribution percentages on each result
     * and serializes every item to the requirements of the endpoint
     * @return Collection|mixed[] Serialized version of the results to be used in the response body
     */
    protected function parse(): Collection
    {
        $total = $this->getTotalAmountOfResults();

        // parse
        return $this->statistics->map(function ($statistic) use ($total) {
            return $this->parseSingleItem($statistic, $total);
        });
    }

    /**
     * Calculates the total amount of the results
     */
    protected function getTotalAmountOfResults(): int
    {
        return $this->statistics->sum('amount');
    }

    /**
     * Gets the table data to be used in the response body
     */
    protected function createTableData(Collection $results): array
    {
        return $results->toArray();
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
     * Gets the chart to be used in response. If the columns are empty,
     * no chart will be produced
     */
    protected function createChartData(Collection $results): array
    {
        $columns = $this->getChartColumns();

        if (empty($columns)) {
            return [];
        }

        return (new StatsChartTableBuilder())->setColumns($columns)
            ->setResults($results)
            ->build();
    }

    /**
     * Mutate the DistributionDTO to the requirements of the endpoint and
     * calculates the distribution percentage
     */
    abstract protected function parseSingleItem(DistributionDTO $item, int $total): object;
}