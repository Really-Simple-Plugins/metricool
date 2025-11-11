<?php

namespace Metricool\Controllers;

use Metricool\App;
use Metricool\Helpers\Event;
use Metricool\Helpers\MetricoolUrl;
use Metricool\Helpers\PostHelper;
use Metricool\Interfaces\ControllerInterface;
use Metricool\Traits\HasAllowlistControl;
use Metricool\Traits\HasViews;

/**
 * This controller manages all the buttons and interactions with the Metricool dashboard
 */
class MetricoolActionsController implements ControllerInterface
{
    use HasAllowlistControl;
    use hasViews;

    public function register(): void
    {
        if ($this->userCanManage()) {
            // Handle actions from the Metricool plugin through the admin init hook.
            add_action('admin_init', [$this, 'handleActions']);

            // Add plugin buttons to the post tables
            $this->addColumnToPostTables();
        }
    }

    /**
     * Handles actions from the Metricool dashboard.
     */
    public function handleActions(): void
    {
        $request = App::provide('request')->fromGlobal();

        // Don't do anything when no action or nonce
        if ($request->isEmpty('metricool_action') && $request->isEmpty('_metricool_action_nonce')) {
            return;
        }

        // Validate nonce
        if ($request->has('_metricool_action_nonce')) {
            if (!wp_verify_nonce($request->get('_metricool_action_nonce'), 'metricool_action')) {
                wp_die(__('Invalid nonce.'));
            }
        }

        // Execute the action
        switch ($request->get('metricool_action')) {
            case 'share_post':
                $this->handleSharePostAction();
                break;
        }
    }

    /**
     * Adds a Metricool column to the post tables and set the column's content
     */
    public function addColumnToPostTables()
    {
        $post_types = PostHelper::getPublicPostTypes();
        foreach ($post_types as $post_type) {
            // Add the column to the post table
            add_filter("manage_{$post_type}_posts_columns", [$this, 'insertPostsColumnHeader']);
            // Add the content to the column
            add_action("manage_{$post_type}_posts_custom_column", [$this, 'insertPostsColumnContent'], 10, 2);
        }
    }

    /**
     * Sets the metricool column header to the post tables
     */
    public function insertPostsColumnHeader(array $columns): array
    {
        $columns['metricool'] = 'Metricool';

        return $columns;
    }

    /**
     * Sets the content of the metricool column
     */
    public function insertPostsColumnContent(string $column_name, int $post_id)
    {
        if ($column_name === 'metricool') {
            $this->renderSharePostButton($post_id);
        }
    }

    /**
     * Renders the share button for a specific post
     */
    public function renderSharePostButton(int $post_id)
    {
        if (get_post_status($post_id) === 'publish') {
            $url = App::env('plugin.dashboard_url') . '&' . http_build_query([
                    'metricool_action' => 'share_post',
                    'metricool_post_id' => $post_id,
                    // todo: check if nonce can be set when button is rendered with javascript
                    '_metricool_action_nonce' => wp_create_nonce('metricool_action'),
                ]);

            $this->render('admin/buttons/share-post', [
                'url' => $url
            ]);
        }
    }

    /**
     * Redirects to the Metricool create post screen with the content and media
     */
    protected function handleSharePostAction(): void
    {
        $request = App::provide('request')->fromGlobal();

        if ($request->isEmpty('metricool_post_id')) {
            return;
        }

        $postId = $request->get('metricool_post_id');

        // Check if post exists
        if (!get_post_status($postId)) {
            return;
        }

        // todo: Check post content with product owner
        $content = get_the_title($postId) . ' - ' . get_permalink($postId);
        // todo: Test media (impossible to do locally)
        $media = get_the_post_thumbnail($postId, 'large');

        // Generate the deeplink
        $url = MetricoolUrl::shareUrl($content, $media);

        Event::dispatch(Event::POST_SCHEDULED);

        // Redirect to the deeplink
        header('Location: ' . $url);
        exit();
    }
}