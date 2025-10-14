<?php

namespace Metricool\Http\Metricool\Dto;

class Statistic // ASK -> name too generic?
{
    public int $timestamp;
    public float $hits;

    public function __construct(int $timestamp, float $hits)
    {
        // ASK -> time in constant
        $this->timestamp = $timestamp;
        $this->hits = $hits;
    }

    public function getValue() : float
    {
        return $this->hits;
    }

    public function getTimestamp() : int
    {
        return $this->timestamp;
    }

}
