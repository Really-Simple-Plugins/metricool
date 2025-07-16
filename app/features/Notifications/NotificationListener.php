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
        add_action('metricool_event_' . Event::EXAMPLE_EVENT, [$this, 'handleExampleEvent']);
    }

    /**
     * Handle the example event to update task status.
     */
    public function handleExampleEvent(array $arguments): void
    {
        // todo
        // $this->service->activate(
            // Notices\FailedAuthenticationNotice::IDENTIFIER
        // );
    }
}