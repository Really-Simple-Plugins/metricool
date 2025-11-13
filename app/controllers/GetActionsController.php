<?php

namespace Metricool\Controllers;

use Metricool\App;
use Metricool\Helpers\Event;
use Metricool\Helpers\Request;
use Metricool\Traits\HasNonces;
use Metricool\Helpers\MetricoolUrl;
use Metricool\Interfaces\ControllerInterface;
use Metricool\Traits\HasAllowlistControl;
use Metricool\Traits\HasViews;

/**
 * This controller processes the plugins GET actions.
 */
class GetActionsController implements ControllerInterface
{
    use HasAllowlistControl;
    use hasViews;
    use HasNonces;

    private const COMPATIBLE_POST_TYPES = ['post', 'page'];
    private const POST_COLUMN_KEY = 'metricool';
    private const SHARE_POST_ACTION = 'share_post';

    public function register(): void
    {
        if ($this->userCanManage() === false) {
            return;
        }

        add_action('admin_init', [$this, 'processGetActions']);

        // Add the action column to the view-table.
        foreach (self::COMPATIBLE_POST_TYPES as $postType) {
            add_filter("manage_{$postType}_posts_columns", [$this, 'insertPostsColumnHeader']);
            add_action("manage_{$postType}_posts_custom_column", [$this, 'insertPostsColumnContent'], 10, 2);
        }
    }

    /**
     * Handles known actions from the {@see Request} based on the
     * metricool_action key.
     */
    public function processGetActions(): void
    {
        $request = App::provide('request')->fromGlobal();
        if ($request->isEmpty('metricool_action')) {
            return;
        }

        // Validate nonce or wp_die on empty
        $nonce = $request->getString('_metricool_action_nonce');
        if (empty($nonce) || !$this->verifyNonce($nonce, 'metricool_action')) {
            wp_die(__('Invalid nonce.', 'metricool'));
        }

        switch ($request->getString('metricool_action')) {
            case self::SHARE_POST_ACTION:
                $this->handleSharePostAction($request);
                break;
        }
    }

    /**
     * Sets the metricool column header to the post tables
     */
    public function insertPostsColumnHeader(array $columns): array
    {
        $columns[self::POST_COLUMN_KEY] = 'Metricool';
        return $columns;
    }

    /**
     * Sets the content of the metricool share post column
     */
    public function insertPostsColumnContent(string $columnName, int $postId)
    {
        if (($columnName !== self::POST_COLUMN_KEY) || (get_post_status($postId) !== 'publish')) {
            return;
        }

        $actionableUrl = add_query_arg([
            'metricool_action' => self::SHARE_POST_ACTION,
            'metricool_post_id' => $postId,
            '_metricool_action_nonce' => wp_create_nonce('metricool_action'),
        ], App::env('plugin.dashboard_url'));

        $this->render('admin/buttons/share-post', [
            'actionableUrl' => $actionableUrl
        ]);
    }

    /**
     * Redirects to the Metricool create post screen with the content and media
     */
    protected function handleSharePostAction(Request $request): void
    {
        $postId = $request->getInt('metricool_post_id');
        $postExists = (get_post_status($postId) !== false);

        if ($postExists === false) {
            return; // abort
        }

        $content = get_the_title($postId) . ' - ' . get_permalink($postId);
        $mediaUrl = get_the_post_thumbnail_url($postId, 'large');
        $metricoolCreatePostUrl = MetricoolUrl::createPostUrl($content, $mediaUrl);

        Event::dispatch(Event::POST_SHARED);

        // Redirect to the deeplink
        header('Location: ' . $metricoolCreatePostUrl);
        exit();
    }
}