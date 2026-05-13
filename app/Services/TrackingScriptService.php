<?php

declare(strict_types=1);

namespace Metricool\Services;

class TrackingScriptService
{
    /**
     * Stores the tracking hash in the database
     */
    public function storeTrackingHash(string $hash): self
    {
        update_option('metricool_tracking_script_hash', $hash);

        return $this;
    }

    public function activateTrackingWidget(): self
    {
        update_option('metricool_tracking_script_active', true);

        return $this;
    }

    /**
     * Returns if the user has enabled the widget in the settings
     */
    public function isTrackingWidgetActive(): bool
    {
        return (bool) get_option('metricool_tracking_script_active', false);
    }

    /**
     * Returns the hash from settings
     */
    public function getTrackingHash(): string
    {
        return (string) get_option('metricool_tracking_script_hash', '');
    }
}
