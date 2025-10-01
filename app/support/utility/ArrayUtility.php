<?php

namespace Metricool\Utility;

/**
 * Utility class for arrays
 */
class ArrayUtility
{
    public static function sumValues(array $data): float
    {
        $values = array_map('floatval', $data);
        return array_sum($values);
    }
}