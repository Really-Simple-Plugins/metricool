<?php

namespace Metricool\Http\Metricool\Entities\Models;

use Carbon\Carbon;

class Statistic // ASK -> name too generic?
{
    public Carbon $date;
    public float $value;

    public function __construct(int $timestamp, float $value)
    {
        // ASK -> time in constant
        $this->date = Carbon::createFromTimestamp($timestamp / 1000);
        $this->value = $value;
    }

    public function formattedDateString() : string
    {
        return $this->date->format('j M');
    }
}
