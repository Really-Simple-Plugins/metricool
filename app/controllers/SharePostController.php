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

class SharePostController implements ControllerInterface
{
    use HasAllowlistControl;
    use hasViews;
    use HasNonces;

    private const DEFAULT_POST_TYPES = ['post', 'page'];
    private const POST_COLUMN_KEY = 'metricool';
    private const SHARE_POST_ACTION = 'share_post';

    public function register(): void
    {
        if ($this->userCanManage() === false) {
            return;
        }

        add_action('init', [$this, 'registerSharePostColumn']);
        add_action('admin_init', [$this, 'processShareAction']);
        add_action('admin_enqueue_scripts', [$this, 'enqueueGeneralAdminStyles']);
        add_filter('default_hidden_columns', [$this, 'filterDefaultHiddenColumns'], 10, 2);
    }

    /**
     * Registers the Metricool share post column on all public post types. Due
     * to {@see filterDefaultHiddenColumns} the column will be hidden by default
     * on all post types except the ones defined in {@see DEFAULT_POST_TYPES}.
     */
    public function registerSharePostColumn(): void
    {
        foreach ($this->getAllPublicPostTypes() as $postType) {
            add_filter("manage_{$postType}_posts_columns", [$this, 'insertPostTableColumn']);
            add_action("manage_{$postType}_posts_custom_column", [$this, 'insertPostTableColumnContent'], 10, 2);
        }
    }

    /**
     * Retrieves all public and viewable post types.
     */
    private function getAllPublicPostTypes(): array
    {
        $postTypes = get_post_types(['public' => true]);
        return array_filter($postTypes, 'is_post_type_viewable');
    }

    /**
     * Method is used for hiding the custom column by default on all post type
     * list table screens except the configured default post types. Users can
     * still enable the column manually through Screen Options.
     */
    public function filterDefaultHiddenColumns(array $hiddenColumns, \WP_Screen $currentScreen): array
    {
        // Only act on post type list tables like edit-post, edit-page, etc.
        if (strpos($currentScreen->id, 'edit-') !== 0) {
            return $hiddenColumns;
        }

        if (empty($currentScreen->post_type)) {
            return $hiddenColumns;
        }

        // Keep column visible if post type is in the default list.
        if (in_array($currentScreen->post_type, self::DEFAULT_POST_TYPES, true)) {
            return $hiddenColumns;
        }

        // Hide the POST_COLUMN_KEY column by default if not already hidden.
        if (!in_array(self::POST_COLUMN_KEY, $hiddenColumns, true)) {
            $hiddenColumns[] = self::POST_COLUMN_KEY;
        }

        return $hiddenColumns;
    }

    /**
     * Handles known actions from the {@see Request} based on the
     * metricool_action key.
     */
    public function processShareAction(): void
    {
        $request = App::provide('request')->fromGlobal();
        $pageIsDashboardPage = ($request->getString('page') === 'metricool');
        $actionIsShareAction = ($request->getString('metricool_action') === self::SHARE_POST_ACTION);

        if (!$pageIsDashboardPage || !$actionIsShareAction) {
            return; // abort
        }

        // Validate nonce or wp_die on empty
        $nonce = $request->getString('_metricool_action_nonce');
        if (empty($nonce) || !$this->verifyNonce($nonce, 'metricool_action')) {
            wp_die(__('Invalid nonce.', 'metricool'));
        }

        $this->handleSharePostAction($request);
    }

    /**
     * Sets the metricool column header to the post tables
     */
    public function insertPostTableColumn(array $columns): array
    {
        $columns[self::POST_COLUMN_KEY] = 'Metricool';
        return $columns;
    }

    /**
     * Method enqueues the css for general admin styles. With it, we style
     * the metricool post table column width.
     */
    public function enqueueGeneralAdminStyles(): void
    {
        wp_enqueue_style(
            'metricool-admin-general-styles',
            App::env('plugin.url') . 'assets/css/admin.css',
            [],
            App::env('plugin.version')
        );
    }

    /**
     * Sets the content of the metricool share post column
     */
    public function insertPostTableColumnContent(string $columnName, int $postId)
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

        // In Metricool the user can "schedule" the post they share from WP.
        // That's where the naming mismatch comes from. Share <-> Schedule
        Event::dispatch(Event::POST_SCHEDULED);

        // Redirect to the deeplink
        header('Location: ' . $metricoolCreatePostUrl);
        exit();
    }
}