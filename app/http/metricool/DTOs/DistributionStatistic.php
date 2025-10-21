<?php

namespace Metricool\Http\Metricool\DTOs;

/**
 * This class represents a DistributionStatistic of one of the results of
 * the DistributionStatistic Entity.
 * {@see \Metricool\Http\Metricool\Entities\DistributionStatistics::hydrateItem}
 */
abstract class DistributionStatistic extends DTO
{
    public string $name;
    public string $metric;
    public int $amount;
    public float $percentage;

    public function __construct(string $name, string $metric, int $amount, float $percentage)
    {
        $this->name = $name;
        $this->metric = $metric;
        $this->amount = $amount;
        $this->percentage = $percentage;
    }
}