<?php

declare(strict_types=1);

namespace Metricool\Features\TaskManagement;

if (!defined('ABSPATH')) {
    exit;
}

use Metricool\Interfaces\FeatureInterface;
use Metricool\Interfaces\TaskInterface;

class TaskManagementController implements FeatureInterface
{
    private TaskManagementEndpoints $endpoints;
    private TaskManagementService $service;
    private TaskManagementListener $listener;

    public function __construct(TaskManagementService $service, TaskManagementEndpoints $endpoints, TaskManagementListener $listener)
    {
        $this->service = $service;
        $this->endpoints = $endpoints;
        $this->listener = $listener;
    }

    public function register(): void
    {
        $this->endpoints->register();
        $this->listener->listen();

        $this->initiateTasks();
        add_action('metricool_plugin_version_upgrade', [$this, 'upgradeTasks']);
    }

    /**
     * This method returns an array of Task class-strings that should be added
     * to the database.
     *
     * @internal New tasks should be added here. Upgrade the task version if the
     * task should be updated. If a task should be removed, remove the task from
     * this list.
     *
     * @return array<int,class-string<TaskInterface>> Array of Task class-strings
     */
    private function getTaskObjects(): array
    {
        return [
            Tasks\TwitterTask::class,
            Tasks\LinkedInTask::class,
            Tasks\HistoricalDataTask::class,
            Tasks\FirstConnectionTask::class,
            Tasks\SchedulePostTask::class,
        ];
    }

    /**
     * This method adds the initial tasks to the database if they are not
     * already present.
     * @throws \Exception If task class cannot be instantiated
     */
    private function initiateTasks(): void
    {
        if ($this->service->hasTasks()) {
            return;
        }

        $this->service->addTasks(
            $this->getTaskObjects()
        );
    }

    /**
     * This method makes sure that if new tasks are added in the update that
     * these tasks are added in the database. Existing tasks will be updated
     * if the version is higher than the current existing task with the same id.
     * @throws \Exception If task class cannot be instantiated
     */
    public function upgradeTasks(): void
    {
        if ($this->service->hasTasks() === false) {
            return; // Tasks will be added by initiateTasks()
        }

        $this->service->upgradeTasks(
            $this->getTaskObjects()
        );
    }
}
