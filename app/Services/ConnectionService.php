<?php

namespace Metricool\Services;

class ConnectionService
{
    public const LOST_CONNECTION_OPTION = 'metricool_lost_connection';

    private OptionsService $options;

    public function __construct(OptionsService $options)
    {
        $this->options = $options;
    }

    /**
     * End the plugin connection after losing connection to Metricool.
     * Clears the authentication tokens and userId, but keeps the tracking
     * widget active so the website does not lose data. Enables the lost
     * connection state so the lost connection modal can be shown.
     */
    public function handleLostConnection(): void
    {
        $this->options->wipe(false, [
            TrackingScriptService::OPTION_TRACKING_HASH,
            TrackingScriptService::OPTION_TRACKING_ACTIVE,
        ]);

        $this->enableLostConnection();
    }

    /**
     * Enable the lost connection state
     */
    public function enableLostConnection(): bool
    {
        return update_option(self::LOST_CONNECTION_OPTION, true, false);
    }

    /**
     * Disable the lost connection state
     */
    public function disableLostConnection(): bool
    {
        return delete_option(self::LOST_CONNECTION_OPTION);
    }

    /**
     * Check if sign in modal should be shown after the connection to
     * Metricool was lost
     */
    public function isLostConnection(): bool
    {
        return (bool) get_option(self::LOST_CONNECTION_OPTION, false);
    }
}
