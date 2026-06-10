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

        if ($private) {
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Bulk delete has no WP API equivalent; cache is flushed below.
            $result = $wpdb->query(
                $wpdb->prepare(
                    "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s OR option_name LIKE %s",
                    'metricool_%',
                    '_metricool_%'
                )
            );
        } else {
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Bulk delete has no WP API equivalent; cache is flushed below.
            $result = $wpdb->query(
                $wpdb->prepare(
                    "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s",
                    'metricool_%'
                )
            );
        }

        // Make sure deleted options are not cached
        if (function_exists('wp_cache_flush')) {
            wp_cache_flush();
        }

        return $result !== false;
    }
}
