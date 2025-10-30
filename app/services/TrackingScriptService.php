<?php

namespace Metricool\Services;

class TrackingScriptService
{
    /**
     * Checks if a hash is set and if the user has enabled the widget
     */
    public function trackingScriptActive(): bool
    {
        return !empty($this->getTrackingHash()) && $this->getTrackingWidgetActive();
    }

    /**
     * Returns if the user has enabled the widget in the settings
     */
    public function getTrackingWidgetActive(): bool
    {
        return get_option('metricool_tracking_script_active') ?? true;
    }

    /**
     * Returns the hash from settings
     */
    public function getTrackingHash(): string
    {
        return get_option('metricool_tracking_script_hash') ?? '';
    }

}