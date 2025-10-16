<?php

namespace Metricool\Http\Metricool\Dto;

/**
 * This class represents a TimelineStatistic of one of the results of
 * the TimelineStatistic Entity. Every result of the Metricool timeline can
 * be hydrated into this DTO.
 * {@see \Metricool\Http\Metricool\Entities\TimelineStatistics::hydrateItem()}
 */
class TimelineStatisticDTO
{
    public int $timestamp;
    public float $hits;

    /**
     * Constructor to fill all the properties of the TimelineStatistic
     */
    public function __construct(int $timestamp, float $hits)
    {
        $this->timestamp = $timestamp;
        $this->hits = $hits;
    }

    /**
     * Gets the hits
     */
    public function getHits() : float
    {
        return $this->hits;
    }

    /**
     * Gets the timestamp
     */
    public function getTimestamp() : int
    {
        return $this->timestamp;
    }

}
