<?php

declare(strict_types=1);

namespace Metricool\Features\AdminNotices\Notices;

use Metricool\Features\AdminNotices\AbstractAdminNotice;
use Metricool\Support\Helpers\Storages\EnvironmentConfig;

final class UpgradeNotice extends AbstractAdminNotice
{
    public const IDENTIFIER = 'upgrade';

    public function __construct(EnvironmentConfig $env)
    {
        parent::__construct($env);
    }

    /**
     * @inheritDoc
     */
    protected function canDisplay(): bool
    {
        $screen = get_current_screen();

        return get_option('metricool_show_upgrade_notice', false)
            && $screen !== null
            && $screen->id !== 'toplevel_page_metricool';
    }

    /**
     * @inheritDoc
     */
    public function isSnoozable(): bool
    {
        return false;
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
