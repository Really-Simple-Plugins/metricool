<?php

namespace Metricool\Services;

class TrackingScriptService
{
    /**
     * Checks if a hash is set and if the user has enabled the widget
     */
    public function canRenderTrackingScript(): bool
    {
        return strlen($this->getTrackingHash()) > 0 && $this->isTrackingWidgetActive();
    }

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