<?php

namespace Metricool\Services;

class TrackingScriptService
{
    public function getTrackingWidgetActive(): bool
    {
        return get_option('metricool_tracking_script_active');
    }

    public function getTrackingHash()
    {
        return get_option('metricool_tracking_script_hash');
    }

    public function trackingScriptActive(): bool
    {
        return !empty($this->getTrackingHash()) && !empty($this->getTrackingWidgetActive());
    }
}