<?php

namespace Metricool\Helpers;

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
}
