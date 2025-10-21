<?php

namespace Metricool\Http\Metricool\DTOs\DistributionStatistics;

use Locale;
use Metricool\Http\Metricool\DTOs\DistributionStatistic;

/**
 * @property string $country
 * @property float $visitors
 * @property float $percentage
 */
class CountryDTO extends DistributionStatistic
{
    /**
     * Returns the full country name from the 2-letter country code
     */
    public function countryName()
    {
        return Locale::getDisplayRegion('-' . $this->metric, get_user_locale());
    }

    public function getCountryAttribute(): string
    {
        return $this->countryName();
    }

    public function getVisitorsAttribute(): float
    {
        return $this->amount;
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return [
            'country' => $this->country,
            'visitors' => $this->visitors,
            'percentage' => $this->percentage
        ];
    }
}