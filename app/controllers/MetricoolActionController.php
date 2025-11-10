<?php

namespace Metricool\Controllers;

use Metricool\App;
use Metricool\Helpers\Event;
use Metricool\Helpers\MetricoolUrl;
use Metricool\Helpers\PostHelper;
use Metricool\Interfaces\ControllerInterface;
use Metricool\Traits\HasAllowlistControl;
use Metricool\Traits\HasViews;

class MetricoolActionController implements ControllerInterface
{
    use HasAllowlistControl;
    use hasViews;

    public function register(): void
    {
        if ($this->userCanManage()) {
            // Add plugin buttons to the post tables
            $this->addColumnToPostTables();
        }

        add_action('load-toplevel_page_metricool', [$this, 'handleAction']);
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
     * Adds the metricool column header to the post tables
     */
    public function insertPostsColumnHeader(array $columns): array
    {
        $columns['metricool'] = 'Metricool';

        return $columns;
    }

    /**
     * Inserts the content into the metricool column
     */
    public function insertPostsColumnContent(string $column_name, int $post_id)
    {
        if ($column_name === 'metricool') {
            $this->renderShareButton($post_id);
        }
    }

    /**
     * Renders the share button for a specific post in the posts table
     */
    protected function renderShareButton(int $post_id)
    {
        if (get_post_status($post_id) === 'publish') {
            $content = get_the_title($post_id) . ' - ' . get_permalink($post_id);
            $media = get_the_post_thumbnail($post_id, 'large');

            $url = App::env('plugin.dashboard_url') . '&' . http_build_query([
                    'metricool_action' => 'create_post',
                    'metricool_post_content' => $content,
                    'metricool_post_media' => $media,
                    '_metricool_action_nonce' => wp_create_nonce('metricool_action'),
                ]);

            $this->render('admin/buttons/share-post', [
                'url' => $url
            ]);
        }
    }

    /**
     * Handles actions from the Metricool dashboard.
     */
    public function handleAction(): void
    {
        // don't do anything when no action or nonce
        if (!isset($_REQUEST['metricool_action']) && !isset($_REQUEST['_metricool_action_nonce'])) {
            return;
        }

        // validate nonce
        if (isset($_REQUEST['_metricool_action_nonce'])) {
            if (!wp_verify_nonce($_REQUEST['_metricool_action_nonce'], 'metricool_action')) {
                wp_die(__('Invalid nonce.'));
            }
        }

        // execute the action
        switch ($_REQUEST['metricool_action']) {
            case 'create_post':
                $this->handleCreatePostAction();
                break;
        }
    }

    /**
     * Redirects to the Metricool create post screen with the content and media
     */
    protected function handleCreatePostAction(): void
    {
        if (!isset($_REQUEST['metricool_post_content'])) {
            return;
        }

        $content = $_REQUEST['metricool_post_content'];
        $media = $_REQUEST['metricool_post_media'] ?? null;

        $url = MetricoolUrl::shareUrl($content, $media);

        Event::dispatch(Event::POST_SCHEDULED);

        header('Location: ' . $url);
        exit();
    }
}