<?php

namespace Metricool\Traits;

/**
 * Trait to hydrate results from an Entity into an array of object
 * {@see \Metricool\Http\Metricool\Entities\TimelineStatistics}
 */
trait isHydratable
{
    protected bool $shouldHydrate = true;

    /**
     * Hydrate raw data into objects
     * @param array $data
     * @return array
     */
    public function hydrate(array $data): array
    {
        $hydrated = [];
        foreach ($data as $item) {
            $hydrated[] = $this->hydrateItem($item);
        }
        return $hydrated;
    }

    /**
     * Hydrate a single item - override in classes using this trait
     * @param mixed $item
     * @return mixed
     */
    abstract protected function hydrateItem($item);
}
