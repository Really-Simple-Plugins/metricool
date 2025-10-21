<?php

namespace Metricool\Http\Metricool\DTOs\TimelineStatistics;

use Metricool\Http\Metricool\DTOs\DTO;

/**
 * This class represents a TimelineStatistic of one of the results of
 * the TimelineStatistic Entity. Every result of the Metricool timeline can
 * be hydrated into this DTO.
 * {@see \Metricool\Http\Metricool\Entities\TimelineStatistics::hydrateItem()}
 */
class TimelineDTO extends DTO
{
    public int $timestamp;
    public float $amount;

    /**
     * Constructor to fill all the properties of the TimelineStatistic
     */
    public function __construct(int $timestamp, float $amount)
    {
        $this->timestamp = $timestamp;
        $this->amount = $amount;
    }

    public function toArray() : array
    {
        return [
            'timestamp' => $this->timestamp,
            'amount' => $this->amount
        ];
    }
}
