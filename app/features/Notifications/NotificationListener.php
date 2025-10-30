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
     * @param array $socialNetworks List of social media connections filtered from the "networksData"
     * /v2/settings/brands Metricool API response
     */
    public function handleConnectedSocialNetworks(array $socialNetworks): void
    {
        if (count($socialNetworks) > 0) {
            $this->service->deactivate(Notices\FirstConnectionNotice::IDENTIFIER);
        }
    }
}