<?php

namespace Metricool\Traits;

use Metricool\Helpers\Collection;

/**
 * Trait to hydrate results from an Entity into an array of object
 * {@see \Metricool\Http\Metricool\Entities\TimelineStatistics}
 */
trait isHydratable
{
    protected bool $shouldHydrate = true;

    /**
     * Hydrate raw data into objects.
     * Use this trait on any Entity that pulls data from an API to hydrate the
     * results into objects.
     */
    public function hydrateResults(array $results): Collection
    {
        $hydrated = [];
        foreach ($results as $key => $item) {
            $hydrated[] = $this->hydrateItem($key, $item);
        }
        return new Collection($hydrated);
    }

    /**
     * Hydrate a single item - override in classes using this trait
     * Should return the object that represents the result
     */
    abstract protected function hydrateItem($key, $item);
}
