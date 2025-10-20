<?php

namespace Metricool\Http\Metricool\DTOs;

/**
 * DTO class to store arrays into an object
 * Dynamically access properties through magic method.
 * You can define an accessor with (for example) getCountryAttribute()
 * $dto->country will call the getCountryAttribute accessor method
 */
abstract class DTO
{
    public function __get($key)
    {
        $method = 'get' . ucfirst($key) . 'Attribute';

        if (method_exists($this, $method)) {
            return $this->$method();
        }

        return null;
    }

    /**
     * Returns the serialized version of the DTO
     */
    abstract public function toArray(): array;
}