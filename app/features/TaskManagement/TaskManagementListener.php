<?php

namespace Metricool\Features\TaskManagement;

use Metricool\App;
use Metricool\Features\TaskManagement\Tasks\HistoricalDataTask;
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

        add_action('metricool_event_' . Event::CONNECTED_NETWORKS_DATA_LOADED, [$this, 'handleConnectedNetworks']);
        add_action('metricool_event_' . Event::SUBSCRIPTION_DATA_LOADED, [$this, 'handleSubscriptionLoaded']);
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
     * Completes tasks based on the keys and values of the connections array.
     */
    public function handleConnectedNetworks($connections): void
    {
        if (!count($connections)) {
            return;
        }

        $connectTasks = [
            'facebookData' => Tasks\TwitterTask::class,
            'linkedinData' => Tasks\LinkedInTask::class,
        ];

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

    /**
     * This even receives the response of the /v2/profile/subscription endpoint.
     * Completes HistoricalDataTask
     */
    public function handleSubscriptionLoaded(array $subscription): void
    {
        $isPremium = strtolower($subscription['planId']) !== 'free';

        if ($isPremium) {
            $this->service->completeTask(HistoricalDataTask::IDENTIFIER);
        }
    }
}