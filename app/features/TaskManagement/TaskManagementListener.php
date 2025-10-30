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

        add_action('metricool_event_' . Event::CONNECTED_SOCIAL_NETWORKS_DATA_LOADED, [$this, 'handleSocialConnectedNetworks']);
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
     * @param array $socialNetworks List of social media connections filtered from the "networksData"
     * /v2/settings/brands Metricool API response
     */
    public function handleSocialConnectedNetworks(array $socialNetworks): void
    {
        if (!count($socialNetworks)) {
            return;
        }

        // Complete the FirstConnectionTask when a social network is connected
        if (count($socialNetworks) > 0) {
            $this->service->completeTask(Tasks\FirstConnectionTask::IDENTIFIER);
        }

        // Complete these tasks when a specific social network is connected
        $connectNetworkTasks = [
            'facebookData' => Tasks\TwitterTask::class,
            'linkedinData' => Tasks\LinkedInTask::class,
        ];

        foreach ($socialNetworks as $networkName) {
            // Find a task associated with this network and complete it
            if (array_key_exists($networkName, $connectNetworkTasks)) {
                $this->service->completeTask(
                    $connectNetworkTasks[$networkName]::IDENTIFIER
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