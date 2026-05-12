<?php

declare(strict_types=1);

namespace Metricool\Features\AdminNotices;

use Metricool\Support\Helpers\Storages\EnvironmentConfig;
use Metricool\Traits\HasViews;

abstract class AbstractAdminNotice
{
    use HasViews {
        render as protected traitRender;
    }

    /**
     * Override this constant to define the unique identifier of the notice.
     */
    public const IDENTIFIER = '';

    /**
     * These constants can be used in the REST endpoint to identify the action being performed (dismiss or snooze)
     */
    public const DISMISS_NOTICE_ACTION = 'dismiss';
    public const SNOOZE_NOTICE_ACTION = 'snooze';

    protected EnvironmentConfig $env;

    public function __construct(EnvironmentConfig $env)
    {
        $this->env = $env;
    }

    /**
     * Get the unique identifier
     */
    public function getId(): string
    {
        return static::IDENTIFIER;
    }

    /**
     * Returns whether the notice can be permanently dismissed
     */
    public function isDismissable(): bool
    {
        return true;
    }

    /**
     * Returns whether the notice can be snoozed (remind me later)
     */
    public function isSnoozable(): bool
    {
        return false;
    }

    /**
     * Returns the snooze duration in days
     */
    public function getSnoozeDays(): int
    {
        return 1;
    }

    /**
     * Returns whether the notice should be displayed
     */
    public function shouldDisplay(): bool
    {
        return $this->canDisplay() && !$this->isDismissed() && !$this->isSnoozed();
    }

    /**
     * Permanently dismiss the notice
     */
    public function dismiss(): void
    {
        update_option('metricool_notice_' . $this->getId() . '_dismissed', true, false);
    }

    /**
     * Snooze the notice for the configured number of days
     */
    public function snooze(): void
    {
        $snoozedUntil = time() + ($this->getSnoozeDays() * DAY_IN_SECONDS);
        update_option('metricool_notice_' . $this->getId() . '_snoozed_until', $snoozedUntil, false);
    }

    /**
     * Check if the notice has been permanently dismissed
     */
    protected function isDismissed(): bool
    {
        return (bool) get_option('metricool_notice_' . $this->getId() . '_dismissed', false);
    }

    /**
     * Check if the notice is currently snoozed
     */
    protected function isSnoozed(): bool
    {
        $snoozedUntil = (int) get_option('metricool_notice_' . $this->getId() . '_snoozed_until', 0);

        if ($snoozedUntil === 0) {
            return false;
        }

        return time() < $snoozedUntil;
    }

    /**
     * Returns the URL for the call-to-action button, or empty string if none
     */
    public function getCtaUrl(): string
    {
        return '';
    }

    /**
     * Returns the label for the call-to-action button, or empty string if none
     */
    public function getCtaLabel(): string
    {
        return '';
    }

    /**
     * Render the notice
     */
    public function render(): void
    {
        $this->traitRender('admin/notices/layout', $this->viewData());
    }

    /**
     * Get the data passed to the notice layout view
     */
    protected function viewData(): array
    {
        $restUrl = rest_url(
            $this->env->getString('http.namespace')
            . '/'
            . $this->env->getString('http.version')
            . '/admin-notices/'
            . $this->getId()
        );

        return [
            'noticeId' => $this->getId(),
            'logoUrl' => $this->env->getUrl('plugin.assets_url') . 'img/mc-logo.svg',
            'restUrl' => $restUrl,
            'isDismissable' => $this->isDismissable(),
            'isSnoozable' => $this->isSnoozable(),
            'ctaUrl' => $this->getCtaUrl(),
            'ctaLabel' => $this->getCtaLabel(),
            'nonce' => wp_create_nonce('metricool_nonce'),
            'wpNonce' => wp_create_nonce('wp_rest'),
            'content' => $this->view($this->getContentView(), $this->getContentVariables()),
        ];
    }

    /**
     * Notice-specific display conditions
     */
    abstract protected function canDisplay(): bool;

    /**
     * Returns the view path for the inner content of the notice
     */
    abstract public function getContentView(): string;

    /**
     * Returns the variables passed to the content view
     */
    abstract public function getContentVariables(): array;
}
