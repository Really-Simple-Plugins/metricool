<?php

namespace Metricool\Services;

class OptionsService
{
    /**
     * Delete all plugin options from the wp_options table
     * @param bool $private Whether to delete private options (prefixed with _)
     */
    public function wipe(bool $private = false): bool
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
