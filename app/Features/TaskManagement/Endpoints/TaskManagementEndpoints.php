<?php

declare(strict_types=1);

namespace Metricool\Features\TaskManagement\Endpoints;

use Metricool\Traits\HasRestAccess;
use Metricool\Interfaces\MultiEndpointInterface;
use Metricool\Features\TaskManagement\TaskManagementService;
use Metricool\Features\TaskManagement\Exceptions\DismissRequiredTaskException;

final class TaskManagementEndpoints implements MultiEndpointInterface
{
    use HasRestAccess;

    private TaskManagementService $service;

    public function __construct(TaskManagementService $service)
    {
        $this->service = $service;
    }

    /**
     * Endpoints are guarded by the {@see TaskManagementFeature}, which only
     * registers these endpoints when the current user can manage the plugin.
     * So this class does not need its own capability check.
     */
    public function isEnabled(): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function registerRoutes(): array
    {
        return [
            'get_tasks' => [
                'methods' => \WP_REST_Server::READABLE,
                'callback' => [$this, 'getTasksCallback'],
            ],
            'dismiss_task' => [
                'methods' => \WP_REST_Server::CREATABLE,
                'callback' => [$this, 'dismissTaskCallback'],
            ],
        ];
    }

    /**
     * Return current tasks as a WP_REST_Response.
     *
     *     GET /wp-json/metricool/v1/get_tasks
     */
    public function getTasksCallback(\WP_REST_Request $request): \WP_REST_Response
    {
        $allTasksAsArray = array_map(function ($task) {
            return $task->toArray();
        }, $this->service->getAllTasks(true));

        return $this->sendHttpResponse(
            array_values($allTasksAsArray) // Keys should be removed
        );
    }

    /**
     * Dismiss a task by taskId.
     *
     *     POST /wp-json/metricool/v1/dismiss_task
     */
    public function dismissTaskCallback(\WP_REST_Request $request): \WP_REST_Response
    {
        $storage = $this->retrieveHttpStorage($request);

        $sanitizedTaskId = $storage->getTitle('taskId');
        $task = $this->service->getTask($sanitizedTaskId);

        if (!$task) {
            return $this->sendHttpErrorResponse(__('Task not found', 'metricool'), null, 404);
        }

        // Attempt to dismiss the task
        try {
            $this->service->dismissTask($task->getId());
        } catch (DismissRequiredTaskException $e) {
            return $this->sendHttpErrorResponse(__('This task cannot be dismissed because it is required', 'metricool'));
        }

        return $this->sendHttpResponse(['taskId' => $sanitizedTaskId], true, __('Task dismissed successfully', 'metricool'));
    }
}
