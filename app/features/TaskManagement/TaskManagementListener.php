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
        // Complete the SchedulePostTask if the action is present in the URL
        if (App::provide('request')->getString('metricool_action') === Tasks\SchedulePostTask::IDENTIFIER) {
            $this->service->completeTask(Tasks\SchedulePostTask::IDENTIFIER);
        }
    }

    /**
     * This event receives the connected social networks data and completes or
     * opens tasks
     * @see Event::CONNECTED_SOCIAL_NETWORKS_DATA_LOADED
     */
    public function handleSocialConnectedNetworks(array $socialNetworks): void
    {
        // Complete or open the tasks that are tied to certain social networks
        $this->handleTasksForNetworks($socialNetworks);

        // Complete or open the FirstConnectionTask based on social network count
        if (count($socialNetworks)) {
            $this->service->openTask(Tasks\FirstConnectionTask::IDENTIFIER);
        } else {
            $this->service->completeTask(Tasks\FirstConnectionTask::IDENTIFIER);
        }
    }

    /**
     * This event receives the active subscription data of the user and completes or opens tasks
     * based on the subscription data. For example, if the user has a premium subscription
     * @see Event::SUBSCRIPTION_DATA_LOADED
     */
    public function handleSubscriptionLoaded(array $subscription): void
    {
        $isPremium = strtolower($subscription['planId']) !== 'free';

        // Complete or open the HistoricalDataTask based on subscription status
        if ($isPremium) {
            $this->service->completeTask(HistoricalDataTask::IDENTIFIER);
        } else {
            $this->service->openTask(HistoricalDataTask::IDENTIFIER);
        }
    }

    /**
     * Complete tasks for specific social networks
     */
    protected function handleTasksForNetworks(array $socialNetworks): void
    {
        // Map social network names to task identifiers
        $connectNetworkTasks = [
            'facebookData' => Tasks\TwitterTask::class,
            'linkedinData' => Tasks\LinkedInTask::class,
        ];

        // Check if any of the connected networks are associated with a task
        $foundNetworkTasks = array_intersect(array_keys($connectNetworkTasks), $socialNetworks);

        // Complete the task for the connected networks
        foreach ($foundNetworkTasks as $networkName) {
            $this->service->completeTask($connectNetworkTasks[$networkName]::IDENTIFIER);
        }

        // Open the tasks for the missing connected networks
        $missingNetworks = array_diff(array_keys($connectNetworkTasks), $foundNetworkTasks);
        foreach ($missingNetworks as $networkName) {
            $this->service->openTask($connectNetworkTasks[$networkName]::IDENTIFIER);
        }
    }
}