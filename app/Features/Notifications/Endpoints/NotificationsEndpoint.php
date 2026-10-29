<?php

declare(strict_types=1);

namespace Metricool\Features\Notifications\Endpoints;

use Metricool\Traits\HasRestAccess;
use Metricool\Interfaces\SingleEndpointInterface;
use Metricool\Features\Notifications\NotificationsService;

final class NotificationsEndpoint implements SingleEndpointInterface
{
    use HasRestAccess;

    public const ROUTE = 'get_notices';

    private NotificationsService $service;

    public function __construct(NotificationsService $service)
    {
        $this->service = $service;
    }

    /**
     * Endpoint is guarded by the {@see NotificationsFeature}, which only
     * registers this endpoint when the current user can manage the plugin.
     * So this class does not need its own capability check.
     */
    public function isEnabled(): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function registerRoute(): string
    {
        return self::ROUTE;
    }

    /**
     * @inheritDoc
     */
    public function registerArguments(): array
    {
        return [
            'methods' => \WP_REST_Server::READABLE,
            'callback' => [$this, 'getNoticesCallback'],
        ];
    }

    /**
     * Return current notices as a WP_REST_Response.
     *
     *     GET /wp-json/metricool/v1/get_notices
     */
    public function getNoticesCallback(\WP_REST_Request $request): \WP_REST_Response
    {
        $allNoticesAsArray = array_map(function ($notice) {
            return $notice->toArray();
        }, $this->service->getAllNotices());

        return $this->sendHttpResponse(
            array_values($allNoticesAsArray) // Keys should be removed
        );
    }
}
