<?php

declare(strict_types=1);

namespace Metricool\Controllers;

use Carbon\Carbon;
use Metricool\Traits\HasViews;
use Metricool\Traits\HasAllowlistControl;
use Metricool\Http\Metricool\MetricoolApi;
use Metricool\Interfaces\ControllerInterface;
use Metricool\Support\Helpers\Storages\RequestStorage;
use Metricool\Support\Helpers\Storages\EnvironmentConfig;

class ReviewController implements ControllerInterface
{
    use HasViews;
    use HasAllowlistControl;

    private const MIN_SESSIONS_COUNT = 20;

    private EnvironmentConfig $env;
    private RequestStorage $request;
    private MetricoolApi $metricoolApi;

    private string $formAction = 'rsp_metricool_review_form_submit';
    private string $formNonce = 'rsp_metricool_review_nonce';

    public function __construct(EnvironmentConfig $env, RequestStorage $request, MetricoolApi $metricoolApi)
    {
        $this->env = $env;
        $this->request = $request;
        $this->metricoolApi = $metricoolApi;
    }

    public function register(): void
    {
        if ($this->adminAccessAllowed() === false) {
            return;
        }

        add_action('admin_notices', [$this, 'showLeaveReviewNotice']);
        add_action('admin_init', [$this, 'processReviewFormSubmit']);
    }

    /**
     * Show a notice to leave a review
     */
    public function showLeaveReviewNotice(): void
    {
        if ($this->canRenderReviewNotice() === false) {
            return;
        }

        $this->render('admin/notices/layout', [
            'logoUrl' => $this->env->getUrl('plugin.assets_url') . 'img/mc-logo.svg',
            'formAction' => $this->formAction,
            'formNonceName' => $this->formNonce,
            'formName' => 'rsp_metricool_review_form',
            'content' => $this->view('admin/notices/review-notice', [
                'reviewUrl' => $this->env->getUrl('plugin.review_url'),
                'reviewMessage' => $this->getReviewNoticeMessage(),
            ]),
        ]);
    }

    /**
     * Process the review form submit
     */
    public function processReviewFormSubmit(): void
    {
        if ($this->request->isEmpty('global.rsp_metricool_review_form')) {
            return;
        }

        $nonce = $this->request->get('global.' . $this->formNonce);
        if (wp_verify_nonce($nonce, $this->formAction) === false) {
            return; // Invalid nonce
        }

        $choice = $this->request->getString('global.rsp_metricool_review_choice');
        if ($choice === 'later') {
            update_option('metricool_review_notice_dismissed_time', time(), false);
            update_option('metricool_review_notice_choice', 'later', false);
        }

        if ($choice === 'never') {
            update_option('metricool_review_notice_choice', 'never', false);
        }
    }

    /**
     * Check if the review notice can be rendered. True when:
     * - The user has not dismissed the notice
     * - The onboarding completion time is suitable for review
     * - The review notice dismissed time has passed
     * - The amount of bookings is greater than the threshold
     * - The user is not on an edit screen
     */
    private function canRenderReviewNotice(): bool
    {
        $previousChoice = get_option('metricool_review_notice_choice');
        if ($previousChoice === 'never') {
            return false;
        }

        if ($this->onboardingCompletedTimestampSuitableForReview() === false) {
            return false;
        }

        if ($this->reviewNoticeDismissedTimeHasPassed() === false) {
            return false;
        }

        // Prevent showing the review on edit screen, as gutenberg removes the
        // class which makes it editable.
        $screen = get_current_screen();
        if ($screen && ('post' === $screen->base)) {
            return false;
        }

        return true;
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
     * Check if the review notice dismissed time is more than 30 days ago.
     */
    private function reviewNoticeDismissedTimeHasPassed(): bool
    {
        $reviewNoticeDismissedTime = get_option('metricool_review_notice_dismissed_time');
        if (empty($reviewNoticeDismissedTime)) {
            return true; // default true to show the notice
        }

        return $this->timestampIsThirtyDaysAgo($reviewNoticeDismissedTime);
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
            // translators: %s is replaced by eiter "x sessions" or "statistics", %2$ and %23$ are replaced with opening and closing a tag containing hyperlink
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
            return 0; // silently fail
        }
    }
}
