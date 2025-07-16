<?php

namespace Metricool\Features\TaskManagement;

use Metricool\Helpers\Event;

class TaskManagementListener
{
    private TaskManagementService $service;

    public function __construct(TaskManagementService $service)
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
        // $this->service->flagTaskUrgent(
            // Tasks\AddMandatoryServiceTask::IDENTIFIER
       // );
    }
}