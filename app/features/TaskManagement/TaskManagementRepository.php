<?php

namespace Metricool\Features\TaskManagement;

use Metricool\Interfaces\TaskInterface;

class TaskManagementRepository
{
    public const OPTION_NAME = 'metricool_tasks';

    /** @var TaskInterface[] */
    private array $tasks = [];

    public function __construct()
    {
        $this->loadTasksFromDatabase();
    }

    /**
     * Retrieve a single task by its ID
     * @throws \InvalidArgumentException when the task cannot be found
     */
    public function getTask(string $taskId): TaskInterface
    {
        if (!isset($this->tasks[$taskId])) {
            throw new \InvalidArgumentException('Unknown task');
        }

        return $this->tasks[$taskId];
    }

    /**
     * Retrieve all registered tasks
     * @return TaskInterface[]
     */
    public function getAllTasks(bool $strict = false): array
    {
        $tasks = $this->tasks;

        // If strict mode is enabled, remove tasks that are hidden
        if ($strict) {
            $tasks = array_filter($tasks, function ($task) {
                return $task->isHidden() === false;
            });
        }

        return $tasks;
    }

    /**
     * Add a single task to the repository
     */
    public function addTask(TaskInterface $task, bool $save = true): void
    {
        $this->tasks[$task->getId()] = $task;

        if ($save) {
            $this->saveTasksToDatabase();
        }
    }

    /**
     * Open a task
     * @throws \InvalidArgumentException when the task cannot be found
     */
    public function openTask(string $taskId): void
    {
        $openTask = $this->getTask($taskId)->open();
        $this->addTask($openTask);
    }

    /**
     * Dismiss a task
     * @throws \InvalidArgumentException when the task cannot be found
     * @throws \Exception when the task cannot be dismissed
     */
    public function dismissTask(string $taskId): void
    {
        $dismissedTask = $this->getTask($taskId)->dismiss();
        $this->addTask($dismissedTask);
    }

    /**
     * Complete a task
     * @throws \InvalidArgumentException when the task cannot be found
     */
    public function completeTask(string $taskId): void
    {
        $completedTask = $this->getTask($taskId)->complete();
        $this->addTask($completedTask);
    }

    /**
     * Hide a task
     * @throws \InvalidArgumentException when the task cannot be found
     */
    public function hideTask(string $taskId): void
    {
        $hiddenTask = $this->getTask($taskId)->hide();
        $this->addTask($hiddenTask);
    }

    /**
     * Flag a task as urgent
     * @throws \InvalidArgumentException when the task cannot be found
     */
    public function flagTaskUrgent(string $taskId): void
    {
        $urgentTask = $this->getTask($taskId)->urgent();
        $this->addTask($urgentTask);
    }

    /**
     * Upgrade a task in the repository. Only replace existing tasks with same
     * identifier if the version is lower than the new task version.
     * @throws \InvalidArgumentException when the task cannot be found
     */
    public function upgradeTask(TaskInterface $task, bool $save = true): void
    {
        $existingTask = $this->getTask($task->getId());

        $taskIsUpdatable = version_compare($existingTask->getVersion(), $task->getVersion(), '<');

        if ($taskIsUpdatable === false) {
            return;
        }

        // Keep current status if new task does not want to reactivate on
        // upgrade and the existing task has a status
        if ($task->isReactivateOnUpgrade() === false) {
            $task->setStatusFromTask($existingTask);
        }

        // Upgrades existing tasks and add new tasks
        $this->addTask($task, $save);
    }

    /**
     * Remove a task from the repository
     */
    public function removeTask(TaskInterface $task, bool $save = true): void
    {
        unset($this->tasks[$task->getId()]);

        if ($save) {
            $this->saveTasksToDatabase();
        }
    }

    /**
     * Remove a task by its ID from the repository
     */
    public function removeTaskById(string $taskId, bool $save = true): void
    {
        if (isset($this->tasks[$taskId])) {
            unset($this->tasks[$taskId]);
        }

        if ($save) {
            $this->saveTasksToDatabase();
        }
    }

    /**
     * Load tasks from the WordPress database
     */
    private function loadTasksFromDatabase(): void
    {
        $storedTasks = get_option(self::OPTION_NAME, []);
        $this->tasks = is_array($storedTasks) ? $storedTasks : [];
    }

    /**
     * Save tasks to the WordPress database
     */
    public function saveTasksToDatabase(): void
    {
        update_option(self::OPTION_NAME, $this->tasks);
    }
}