<?php

namespace Metricool\Http\Metricool\DTOs;

/**
 * This class represents a DistributionDTO of one of the results of
 * the DistributionDTO Entity.
 */
class DistributionDTO extends DTO
{
    public string $name;
    public string $value;
    public int $amount;

    public function __construct(string $name, string $value, int $amount)
    {
        $this->name = $name;
        $this->value = $value;
        $this->amount = $amount;
    }

    public function calculatePercentageFromTotal($total)
    {
        if ($total === 0 || $this->amount === 0) {
            return 0;
        }
        return round((float)(($this->amount / $total) * 100), 3, PHP_ROUND_HALF_UP);
    }
}