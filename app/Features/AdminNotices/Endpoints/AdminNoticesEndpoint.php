<?php

declare(strict_types=1);

namespace Metricool\Features\AdminNotices\Endpoints;

use WP_REST_Server;
use Metricool\Traits\HasRestAccess;
use Metricool\Support\Helpers\Storage;
use Metricool\Interfaces\SingleEndpointInterface;
use Metricool\Features\AdminNotices\AbstractAdminNotice;
use Metricool\Features\AdminNotices\AdminNoticesRepository;

final class AdminNoticesEndpoint implements SingleEndpointInterface
{
    use HasRestAccess;

    public const ROUTE = '/admin-notices/(?P<notice_id>[\w-]+)';

    private AdminNoticesRepository $repository;

    public function __construct(AdminNoticesRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Endpoint is guarded by the {@see AdminNoticesFeature}, which only
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
            'methods' => WP_REST_Server::CREATABLE,
            'callback' => [$this, 'handleNoticeAction'],
            'args' => [
                'notice_id' => [
                    'required' => true,
                    'type' => 'string',
                    'sanitize_callback' => 'sanitize_text_field',
                ],
                'action' => [
                    'required' => true,
                    'type' => 'string',
                    'enum' => [AbstractAdminNotice::DISMISS_NOTICE_ACTION, AbstractAdminNotice::SNOOZE_NOTICE_ACTION],
                ],
            ],
        ];
    }

    /**
     * Handle an action for an admin notice.
     *
     *     POST /wp-json/metricool/v1/admin-notices/{notice_id}
     */
    public function handleNoticeAction(\WP_REST_Request $wpRestRequest): \WP_REST_Response
    {
        $request = new Storage(
            $wpRestRequest->get_params()
        );

        $noticeId = $request->getString('notice_id');
        $action = $request->getString('action');

        $notice = $this->repository->find($noticeId);
        if ($notice === null) {
            return $this->sendHttpErrorResponse('Notice not found', null, 404);
        }

        if ($action === AbstractAdminNotice::DISMISS_NOTICE_ACTION) {
            $notice->dismiss();
        }

        if ($action === AbstractAdminNotice::SNOOZE_NOTICE_ACTION) {
            $notice->snooze();
        }

        return $this->sendHttpResponse(['success' => true]);
    }
}
