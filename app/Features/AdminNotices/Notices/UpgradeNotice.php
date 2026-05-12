<?php

declare(strict_types=1);

namespace Metricool\Features\AdminNotices\Notices;

use Metricool\Features\AdminNotices\AbstractAdminNotice;
use Metricool\Services\DashboardService;
use Metricool\Support\Helpers\Storages\EnvironmentConfig;

final class UpgradeNotice extends AbstractAdminNotice
{
    public const IDENTIFIER = 'upgrade';

    private DashboardService $dashboard;

    public function __construct(EnvironmentConfig $env, DashboardService $dashboard)
    {
        parent::__construct($env);

        $this->dashboard = $dashboard;
    }

    /**
     * @inheritDoc
     */
    protected function canDisplay(): bool
    {
        return !$this->dashboard->isUserOnDashboard() &&
            $this->dashboard->isForcedLogin();
    }

    /**
     * @inheritDoc
     */
    public function isSnoozable(): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function getCtaUrl(): string
    {
        return $this->env->getUrl('plugin.dashboard_url');
    }

    /**
     * @inheritDoc
     */
    public function getCtaLabel(): string
    {
        return __('Sign in now!', 'metricool');
    }

    /**
     * @inheritDoc
     */
    public function getContentView(): string
    {
        return 'admin/notices/upgrade-notice';
    }

    /**
     * @inheritDoc
     */
    public function getContentVariables(): array
    {
        return [];
    }
}
