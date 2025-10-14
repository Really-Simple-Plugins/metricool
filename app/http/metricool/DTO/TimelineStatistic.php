<?php

namespace Metricool\Http\Metricool\Dto;

/**
 * This class represents a TimelineStatistic of one of the results of
 * the TimelineStatistic Entity.
 */
class TimelineStatistic
{
    public int $timestamp;
    public float $hits;

    /**
     * Constructor to fill all the properties of the TimelineStatistic
     * @param int $timestamp
     * @param float $hits
     */
    public function __construct(int $timestamp, float $hits)
    {
        $this->timestamp = $timestamp;
        $this->hits = $hits;
    }

    /**
     * Gets the hits
     * @return float
     */
    public function getHits() : float
    {
        return $this->hits;
    }

    /**
     * Gets the timestamp
     * @return int
     */
    public function getTimestamp() : int
    {
        return $this->timestamp;
    }

}
