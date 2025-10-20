<?php

namespace Metricool\Http\Metricool\DTOs;

/**
 * DTO class to store array into an object
 * You can dynamically access properties into methods.
 * Defining an accessor: public function getCountryAttribute()
 * $dto->country will call the accessor
 */
class DTO
{
    public function __get($key)
    {
        $method = 'get' . ucfirst($key) . 'Attribute';
        if (method_exists($this, $method)) {
            return $this->$method();
        }
        return '...';
    }
}