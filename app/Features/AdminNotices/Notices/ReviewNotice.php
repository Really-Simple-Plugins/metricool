<?php

declare(strict_types=1);

namespace Metricool\Features\AdminNotices\Notices;

use Carbon\Carbon;
use Metricool\Features\AdminNotices\AbstractAdminNotice;
use Metricool\Http\Metricool\MetricoolApi;
use Metricool\Support\Helpers\Storages\EnvironmentConfig;

final class ReviewNotice extends AbstractAdminNotice
{
    public const IDENTIFIER = 'review';

    private const MIN_SESSIONS_COUNT = 20;

    private MetricoolApi $metricoolApi;

    public function __construct(EnvironmentConfig $env, MetricoolApi $metricoolApi)
    {
        parent::__construct($env);

        $this->metricoolApi = $metricoolApi;
    }

    /**
     * @inheritDoc
     */
    protected function canDisplay(): bool
    {
        if ($this->onboardingCompletedTimestampSuitableForReview() === false) {
            return false;
        }

        $screen = get_current_screen();
        if ($screen && ('post' === $screen->base)) {
            return false;
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function isDismissable(): bool
    {
        return true;
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
    public function getSnoozeDays(): int
    {
        return 30;
    }

    /**
     * @inheritDoc
     */
    public function getCtaUrl(): string
    {
        return $this->env->getUrl('plugin.review_url');
    }

    /**
     * @inheritDoc
     */
    public function getCtaLabel(): string
    {
        return __('Leave a review', 'metricool');
    }

    /**
     * @inheritDoc
     */
    public function getContentView(): string
    {
        return 'admin/notices/review-notice';
    }

    /**
     * @inheritDoc
     */
    public function getContentVariables(): array
    {
        return [
            'reviewMessage' => $this->getReviewNoticeMessage(),
        ];
    }

    /**
     * Check if the onboarding completion time is more than 30 days ago.
     */
    private function onboardingCompletedTimestampSuitableForReview(): bool
    {
        $onboardingCompletedTimestamp = get_option('metricool_onboarding_completed');
        if (empty($onboardingCompletedTimestamp)) {
            return false;
        }

        return $this->timestampIsThirtyDaysAgo($onboardingCompletedTimestamp);
    }

    /**
     * Check if the timestamp is more than 30 days ago.
     * @param float|int|string $timestamp
     */
    private function timestampIsThirtyDaysAgo($timestamp): bool
    {
        $timestamp = Carbon::createFromTimestamp($timestamp);
        $thirtyDaysAgo = Carbon::now()->subDays(30);

        return $timestamp->isBefore($thirtyDaysAgo);
    }

    /**
     * Returns the message we render in the notice. It only includes the amount
     * of tracked sessions in the last 30 days if this exceeds
     * {@see MIN_SESSIONS_COUNT}. Otherwise, it will render a general message.
     */
    private function getReviewNoticeMessage(): string
    {
        $mentionedStatistic = esc_html__('statistics', 'metricool');
        $sessionCountLast30Days = $this->getSessionCountLast30Days();

        if ($sessionCountLast30Days > self::MIN_SESSIONS_COUNT) {
            $mentionedStatistic = ($sessionCountLast30Days . ' ' . esc_html__('sessions', 'metricool'));
        }

        return sprintf(
            // translators: %s is replaced by either "x sessions" or "statistics", %2$ and %3$ are replaced with opening and closing a tag containing hyperlink
            __('Hi, Metricool has tracked %s on your site for the last 30 days. If you have a moment, please consider leaving a review on wordpress.org to spread the word. We greatly appreciate it! If you have any questions or feedback, leave us a %2$smessage%3$s.', 'metricool'),
            $mentionedStatistic,
            '<a href="' . $this->env->getUrl('plugin.support_url') . '"  rel="noopener noreferrer"  target="_blank">',
            '</a>'
        );
    }

    /**
     * Return amount of tracked sessions for the last 30 days (default filter)
     * or return zero when the request fails.
     */
    private function getSessionCountLast30Days(): int
    {
        try {
            return $this->metricoolApi->statistics()->visits()->filter([
                'period' => 'last30days',
            ])->get()->sum('amount');
        } catch (\Throwable $e) {
            return 0;
        }
    }
}
