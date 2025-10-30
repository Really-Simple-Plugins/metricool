<?php

namespace Metricool\Features\Notifications;

use Metricool\Helpers\Event;

class NotificationListener
{
    private NotificationsService $service;

    public function __construct(NotificationsService $service)
    {
        $this->service = $service;
    }

    public function listen(): void
    {
        add_action('metricool_event_' . Event::CONNECTED_NETWORKS_DATA_LOADED, [$this, 'handleConnectedNetworks']);
    }

    /**
     * Event receives a list of connections from the "networksData" object of the /v2/settings/brands Metricool API response
     * Dismisses the FirstConnectionNotice
     */
    public function handleConnectedNetworks(array $connections): void
    {
        $connectionNames = array_keys($connections);

        // filter out all non-social networks
        $socialNetworks = array_filter($connectionNames, function ($connectionName) {
            return !str_contains('webData', $connectionName);
        });

        if (count($socialNetworks) > 0) {
            $this->service->deactivate(Notices\FirstConnectionNotice::IDENTIFIER);
        }
    }
}