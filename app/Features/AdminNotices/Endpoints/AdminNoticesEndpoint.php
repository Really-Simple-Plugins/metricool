<?php

declare(strict_types=1);

namespace Metricool\Features\AdminNotices\Endpoints;

use Metricool\Features\AdminNotices\AbstractAdminNotice;
use Metricool\Features\AdminNotices\AdminNoticesRepository;
use Metricool\Interfaces\SingleEndpointInterface;
use Metricool\Support\Helpers\Storage;
use Metricool\Traits\HasAllowlistControl;
use Metricool\Traits\HasRestAccess;

final class AdminNoticesEndpoint implements SingleEndpointInterface
{
    use HasRestAccess;
    use HasAllowlistControl;

    public const ROUTE = '/admin-notices/(?P<notice_id>[\w-]+)';

    private AdminNoticesRepository $repository;

    public function __construct(AdminNoticesRepository $repository)
    {
        $this->repository = $repository;
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
            'methods' => 'POST',
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
