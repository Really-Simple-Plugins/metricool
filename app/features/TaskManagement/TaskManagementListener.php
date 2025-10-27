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

        add_action('metricool_event_' . Event::CONNECTED_BRANDS_DATA_LOADED, [$this, 'handleConnectedBrands']);
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
     * Event receives a list of connections from the "networksData" object of the /v2/settings/brands Metricool API response
     */
    public function handleConnectedBrands($connections)
    {
        $connectTasks = [
            'facebookData' => Tasks\TwitterTask::class,
            'linkedinData' => Tasks\LinkedInTask::class,
        ];

        if (count($connections) > 1) {
            $this->service->completeTask(
                Tasks\FirstConnectionTask::IDENTIFIER
            );

            foreach ($connections as $key => $task) {
                if (array_key_exists($key, $connectTasks)) {
                    $this->service->completeTask(
                        $connectTasks[$key]::IDENTIFIER
                    );
                }
            }
        }
    }
}