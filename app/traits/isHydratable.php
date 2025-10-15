<?php

namespace Metricool\Traits;

/**
 * With this method we can easily access the array contents and the
 * statistic data per array item. Usage can be seen here:
 * {@see \Metricool\Services\AnalyticsService::getTotalAmount}
 * @return Collection<Statistic>
 */

trait isHydratable
{
    protected bool $shouldHydrate = true;

    /**
     * Hydrate raw data into objects
     */
    public function hydrate(array $data): array
    {
        $hydrated = [];
        foreach ($data as $key => $item) {
            $hydrated[] = $this->hydrateItem($key, $item);
        }
        return $hydrated;
    }

    /**
     * Hydrate a single item - override in classes using this trait
     */
    abstract protected function hydrateItem($key, $item);
}
