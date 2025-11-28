<?php

declare(strict_types=1);

namespace Metricool\Services;

class TrackingScriptService
{
    /**
     * Returns if the user has enabled the widget in the settings
     */
    public function isTrackingWidgetActive(): bool
    {
        return get_option('metricool_tracking_script_active', false);
    }

    /**
     * Returns the hash from settings
     */
    public function getTrackingHash(): string
    {
        return get_option('metricool_tracking_script_hash', '');
    }
}
