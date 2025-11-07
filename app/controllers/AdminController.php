<?php

namespace Metricool\Controllers;

use Metricool\App;
use Metricool\Helpers\MetricoolUrl;
use Metricool\Helpers\PostHelper;
use Metricool\Interfaces\ControllerInterface;
use Metricool\Traits\HasAllowlistControl;
use Metricool\Traits\HasViews;

class AdminController implements ControllerInterface
{
    use HasAllowlistControl;
    use hasViews;

    public function register(): void
    {
        if ($this->adminAccessAllowed() === false) {
            return;
        }

        add_filter('plugin_action_links_' . App::env('plugin.base_file'), [$this, 'addPluginSettingsAction']);

        if ($this->userCanManage()) {
            // Add plugin buttons to the post tables
            $this->addColumnToPostTables();
        }
    }

    /**
     * Adds settings and support link to the plugin page
     */
    public function addPluginSettingsAction(array $links): array
    {
        if ($this->userCanManage() === false) {
            return $links;
        }

        $settings_link = '<a href="' . App::env('plugin.dashboard_url') . '">' . esc_html__('Settings', 'metricool') . '</a>';
        array_unshift($links, $settings_link);

        //support
        $support = '<a rel="noopener noreferrer" target="_blank" href="' . esc_attr(App::env('plugin.support_url')) . '">' . esc_html__('Support', 'metricool') . '</a>';
        array_unshift($links, $support);

        return $links;
    }

    /**
     * Adds a column to the post tables and set the column's content
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
     * Renders the share button if the post is published
     */
    protected function renderShareButton(int $post_id)
    {
        if (get_post_status($post_id) === 'publish') {
            $content = get_the_title($post_id) . ' - ' . get_permalink($post_id);
            $media = get_the_post_thumbnail($post_id, 'large');

            $this->render('admin/buttons/share-post', [
                'url' => Metricoolurl::shareUrl($content, $media),
            ]);
        }
    }
}