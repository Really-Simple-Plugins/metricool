<?php

namespace Metricool\Http\Metricool\Entities\Models;

use Carbon\Carbon;

class Statistic // <- name too generic?
{
    public Carbon $date;
    public float $value;

    public function __construct(int $timestamp, float $value)
    {
        $this->date = Carbon::createFromTimestamp($timestamp / SECOND_IN_MILLISECONDS);
        $this->value = $value;
    }

    public function formattedDateString() : string
    {
        return $this->date->format('j M');
    }
}
