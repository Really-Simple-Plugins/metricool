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
        // todo: test this on front-end
        if (count($connections) > 1) {
            $this->service->deactivate(Notices\FirstConnectionNotice::IDENTIFIER);
        }
    }
}