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
        add_action('metricool_event_' . Event::CONNECTED_SOCIAL_NETWORKS_DATA_LOADED, [$this, 'handleConnectedSocialNetworks']);
    }
    
    /**
     * This event receives the connected social networks data and activates or
     * deactivates notices
     * @see Event::CONNECTED_SOCIAL_NETWORKS_DATA_LOADED
     */
    public function handleConnectedSocialNetworks(array $socialNetworks): void
    {
        // Deactivate the FirstConnectionNotice when a social network is connected
        if (count($socialNetworks) > 0) {
            $this->service->deactivate(Notices\FirstConnectionNotice::IDENTIFIER);
        } else {
            $this->service->activate(Notices\FirstConnectionNotice::IDENTIFIER);
        }
    }
}