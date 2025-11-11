<?php

namespace Metricool\Helpers;

use Metricool\App;

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
     * Returns the Metricool share URL for the given content and media
     * This is a deeplink to the create-post screen of the Metricool web app
     */
    public static function shareUrl(string $content, string $media = null): string
    {
        // todo - fetch from settings
        $queryArgs = [
            'blogId' => (defined('METRICOOL_BLOG_ID') ? METRICOOL_BLOG_ID : ''),
            'userId' => (defined('METRICOOL_USER_ID') ? METRICOOL_USER_ID : ''),
            'post.content' => $content,
        ];

        if ($media) {
            $queryArgs['post.media'] = $media;
        }

        return add_query_arg(array_filter($queryArgs), App::env('metricool.create_post_url'));
    }
}
