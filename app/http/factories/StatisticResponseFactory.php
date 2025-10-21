<?php

namespace Metricool\Http\Factories;

use Metricool\Helpers\Collection;
use Metricool\Http\Responses\Statistics\CountriesResponse;
use Metricool\Http\Responses\Statistics\RefererResponse;
use Metricool\Http\Responses\StatisticsResponse;

/**
 * Our statistics endpoint holds different responses per metric. For example, the country response hold the metric
 * value in the country property, while the referrers endpoint holds the metric value into a url property
 * These responses always include tableData and chartData
 */
class StatisticResponseFactory
{
    public static array $responses = [
        'countries' => CountriesResponse::class,
        'referers' => RefererResponse::class
    ];

    /**
     * @throws \InvalidArgumentException
     */
    public static function buildResponse($metric, Collection $results): StatisticsResponse
    {
        // find the response of this metric
        $response = self::getResponseFromMetric($metric);

        return (new $response($results));
    }

    /**
     * @throws \InvalidArgumentException
     */
    public static function getResponseFromMetric($metric)
    {
        if (array_key_exists($metric, self::$responses) === false) {
            throw new \InvalidArgumentException("Metric '$metric' has no response defined.");
        }

        return self::$responses[$metric];
    }
}