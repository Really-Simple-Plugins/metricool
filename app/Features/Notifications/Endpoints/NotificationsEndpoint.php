<?php

declare(strict_types=1);

namespace Metricool\Features\Notifications\Endpoints;

use Metricool\Features\Notifications\NotificationsService;
use Metricool\Interfaces\SingleEndpointInterface;
use Metricool\Traits\HasAllowlistControl;
use Metricool\Traits\HasRestAccess;

final class NotificationsEndpoint implements SingleEndpointInterface
{
    use HasRestAccess;
    use HasAllowlistControl;

    public const ROUTE = 'get_notices';

    private NotificationsService $service;

    public function __construct(NotificationsService $service)
    {
        $this->service = $service;
    }

    /**
     * @inheritDoc
     */
    public function enabled(): bool
    {
        return $this->userCanManage();
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
