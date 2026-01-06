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
        return (bool) get_option('metricool_tracking_script_active', false);
    }

    /**
     * Returns the hash from settings
     */
    public function getTrackingHash(): string
    {
        return (string) get_option('metricool_tracking_script_hash', '');
    }

    /**
     * Saves the tracking hash from the legacy option 'metricool_profile_id' if
     * it exists, and then deletes the legacy option.
     * @internal Call this method when upgrading from legacy version.
     */
    public function upgradeProfileIdToTrackingHash(): void
    {
        $profileId = get_option('metricool_profile_id', '');

        if (!empty(trim($profileId))) {
            update_option('metricool_tracking_script_hash', $profileId, false);
            update_option('metricool_tracking_script_active', true, false);
        }

        delete_option('metricool_profile_id');
    }
}
