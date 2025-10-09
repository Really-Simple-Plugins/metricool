<?php

namespace Metricool\Http\Metricool\Dto;

class Statistic // ASK -> name too generic?
{
    public int $timestamp;
    public float $value;

    public function __construct(int $timestamp, float $value)
    {
        // ASK -> time in constant
        $this->timestamp = $timestamp;
        $this->value = $value;
    }

    public function getValue() : float
    {
        return $this->value;
    }

    public function getTimestamp() : int
    {
        return $this->timestamp;
    }

}
