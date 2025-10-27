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
     *
     * Example:
     * {
     *     "webData": "https://help.metricool.com/es/",
     *     "facebookData": "101307319490812",
     *     "instagramData": "testingmetri",
     *     "threadsData": "testingmetri",
     *     "blueskyData": "testingmetri.bsky.social",
     *     "twitterData": "TestingMetri",
     *     "linkedinData": "urn:li:organization:91711355",
     *     "pinterestData": "testingmetri",
     *     "tiktokData": "testingmetri",
     *     "gbpData": "accounts/114630028650069139274/locations/16265234060702537753",
     *     "youtubeData": "UCYc9UBnvBDUXqpgJZYEurOg",
     *     "twitchData": "868382795",
     *     "facebookAdsData": "act_911576459824189",
     *     "googleAdsData": "8686751192",
     *     "tiktokAdsData": "7186312106294165505"
     * }
     *
     * Completes tasks based on the keys and values of the connections object.
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