<?php

declare(strict_types=1);

namespace Metricool\Support\Helpers;

use Metricool\Bootstrap\App;

class MetricoolUrl
{
    /**
     * Add required query args to a Metricool admin url
     * @param string $url External url to a Metricool admin page
     * @return string The url with the required query args
     */
    public static function adminUrl(string $url): string
    {
        // todo - fetch from settings
        $queryArgs = array_filter([
            'blogId' => (defined('METRICOOL_BLOG_ID') ? METRICOOL_BLOG_ID : ''),
            'userId' => (defined('METRICOOL_USER_ID') ? METRICOOL_USER_ID : ''),
        ]);

        return add_query_arg($queryArgs, $url);
    }


    /**
     * Returns the Metricool create post URL for the given content and media
     * This is a deeplink to the create-post screen of the Metricool web app
     * @param string|false|null $mediaUrl Optional media URL to be included
     *                                    in the post
     */
    public static function createPostUrl(string $content, $mediaUrl = null): string
    {
        // todo - fetch from settings
        $queryArgs = [
            'blogId' => (defined('METRICOOL_BLOG_ID') ? METRICOOL_BLOG_ID : ''),
            'userId' => (defined('METRICOOL_USER_ID') ? METRICOOL_USER_ID : ''),
            'post.content' => $content,
        ];

        if ($mediaUrl) {
            $queryArgs['post.media'] = $mediaUrl;
        }

        return add_query_arg(array_filter($queryArgs), App::getInstance()->env->getUrl('metricool.create_post_url'));
    }
}
