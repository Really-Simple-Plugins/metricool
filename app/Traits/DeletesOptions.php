<?php

namespace Metricool\Traits;

trait DeletesOptions
{
    public function deleteAllOptions(bool $private = false): bool
    {
        global $wpdb;
        $query = "DELETE FROM $wpdb->options WHERE option_name LIKE %s";
        $params = ['metricool_%'];

        if ($private) {
            $query .= " OR option_name LIKE %s";
            $params[] = '_metricool_%';
        }

        $result = $wpdb->query(
            $wpdb->prepare($query, ...$params)
        );

        // Make sure deleted options are not cached
        if (function_exists('wp_cache_flush')) {
            wp_cache_flush();
        }

        return $result !== false;
    }
}
