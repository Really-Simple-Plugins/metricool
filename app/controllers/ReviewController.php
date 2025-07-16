<?php
namespace Metricool\Controllers;

use Carbon\Carbon;
use Metricool\App;
use Metricool\Traits\HasViews;
use Metricool\Traits\HasAllowlistControl;
use Metricool\Interfaces\ControllerInterface;

class ReviewController implements ControllerInterface
{
    use HasViews;
    use HasAllowlistControl;

    private string $reviewAction = 'rsp_metricool_review_form_submit';
    private string $reviewNonceName = 'rsp_metricool_review_nonce';

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

        $reviewMessage = sprintf(
            // translators: %1$d is replaced by the amount of bookings, %2$ and %23$ are replaced with opening and closing a tag containing hyperlink
            __('Hi, Metricool has helped you reach %1$d bookings in the last 30 days. If you have a moment, please consider leaving a review on WordPress.org to spread the word. We greatly appreciate it! If you have any questions or feedback, leave us a %2$smessage%3$s.', 'metricool'),
            3,
            '<a href="' . App::env('plugin.support_url') . '"  rel="noopener noreferrer"  target="_blank">',
            '</a>'
        );

        $this->render('admin/review-notice', [
            'logoUrl' => App::env('plugin.assets_url') . 'img/metricool-icon-256x256.png',
            'reviewUrl' => App::env('metricool.review_url'),
            'reviewMessage' => $reviewMessage,
            'reviewAction' => $this->reviewAction,
            'reviewNonceName' => $this->reviewNonceName,
        ]);
    }

    /**
     * Process the review form submit
     */
    public function processReviewFormSubmit(): void
    {
        if (App::provide('request')->fromGlobal()->isEmpty('rsp_metricool_review_form')) {
            return;
        }

        $request = App::provide('request')->fromGlobal();

        $nonce = $request->get($this->reviewNonceName);
        if (wp_verify_nonce($nonce, $this->reviewAction) === false) {
            return; // Invalid nonce
        }

        $choice = $request->getString('rsp_metricool_review_choice');
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
     * - The company registration time is suitable for review
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

        if ($this->companyRegisteredTimeSuitableForReview() === false) {
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
     * Check if the company registration time is more than 30 days ago.
     */
    private function companyRegisteredTimeSuitableForReview(): bool
    {
        $companyRegistrationStartTime = get_option('metricool_company_registration_start_time');
        if (empty($companyRegistrationStartTime)) {
            return false;
        }

        return $this->timestampIsThirtyDaysAgo($companyRegistrationStartTime);
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
     */
    private function timestampIsThirtyDaysAgo($timestamp): bool
    {
        $timestamp = Carbon::createFromTimestamp($timestamp);
        $thirtyDaysAgo = Carbon::now()->subDays(30);

        return $timestamp->isBefore($thirtyDaysAgo);
    }
}