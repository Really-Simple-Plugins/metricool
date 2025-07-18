<?php

namespace Metricool\Features\TaskManagement;

use Metricool\App;
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
        add_action('load-edit.php', [$this, 'handleEditPageLoad']);
        add_action('metricool_event_' . Event::EXAMPLE_EVENT, [$this, 'handleExampleEvent']);
    }

    /**
     * Handle the edit.php page load to check for tasks.
     */
    public function handleEditPageLoad(): void
    {
        if (App::provide('request')->getString('metricool_action') !== Tasks\SchedulePostTask::IDENTIFIER) {
            return;
        }

         $this->service->dismissTask(
             Tasks\SchedulePostTask::IDENTIFIER,
         );
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