<?php

declare(strict_types=1);

namespace Metricool\Http\Metricool\Entities\Facades;

use Metricool\Http\Metricool\MetricoolClient;
use Metricool\Http\Metricool\Entities\TimelineStatistics;
use Metricool\Http\Metricool\Entities\DistributionStatistics;

/**
 * Facade to access various statistics entities in Metricool.
 */
class StatisticsFacade
{
    protected MetricoolClient $client;

    public function __construct(MetricoolClient $client)
    {
        $this->client = $client;
    }

    /**
     * Returns per-country website visits distribution during the period. Use
     * the {@see filter()} method to filter the results by date range.
     */
    public function countries(bool $useCache = true): DistributionStatistics
    {
        $cacheName = 'metricool_entities_cache_statistics_facade_countries';
        $cache = wp_cache_get($cacheName, 'metricool');
        if ($useCache && !empty($cache)) {
            return $cache;
        }

        $entity = new DistributionStatistics($this->client, 'country');
        wp_cache_set($cacheName, $entity, 'metricool', MINUTE_IN_SECONDS);

        return $entity;
    }

    /**
     * Returns the distribution of website visits by referers during the period.
     * Use the {@see filter()} method to filter the results by date range.
     *
     * @internal 'referers' results in a list of website uri's with the amount
     * of visits from each referer during the period.
     */
    public function referers(bool $useCache = true): DistributionStatistics
    {
        $cacheName = 'metricool_entities_cache_statistics_facade_referers';
        $cache = wp_cache_get($cacheName, 'metricool');
        if ($useCache && !empty($cache)) {
            return $cache;
        }

        $entity = new DistributionStatistics($this->client, 'referers');
        wp_cache_set($cacheName, $entity, 'metricool', MINUTE_IN_SECONDS);

        return $entity;
    }

    /**
     * Returns the distribution of website visits by sources during the period.
     * Use the {@see filter()} method to filter the results by date range.
     *
     * @internal 'sources' results in a list of source-types from which the
     * visits originated, such as 'direct', 'google.com', 'youtube.com', etc.
     */
    public function sources(bool $useCache = true): DistributionStatistics
    {
        $cacheName = 'metricool_entities_cache_statistics_facade_sources';
        $cache = wp_cache_get($cacheName, 'metricool');
        if ($useCache && !empty($cache)) {
            return $cache;
        }

        $entity = new DistributionStatistics($this->client, 'sources');
        wp_cache_set($cacheName, $entity, 'metricool', MINUTE_IN_SECONDS);

        return $entity;
    }

    /**
     * Returns the page view statistics for the website during the period for
     * timeline charts usage.
     */
    public function pageViews(bool $useCache = true): TimelineStatistics
    {
        $cacheName = 'metricool_entities_cache_statistics_facade_page_views';
        $cache = wp_cache_get($cacheName, 'metricool');
        if ($useCache && !empty($cache)) {
            return $cache;
        }

        $entity = new TimelineStatistics($this->client, 'PageViews');
        wp_cache_set($cacheName, $entity, 'metricool', MINUTE_IN_SECONDS);

        return $entity;
    }

    /**
     * Returns the session counts for the website during the period for
     * timeline charts usage
     */
    public function visits(bool $useCache = true): TimelineStatistics
    {
        $cacheName = 'metricool_entities_cache_statistics_facade_visits';
        $cache = wp_cache_get($cacheName, 'metricool');
        if ($useCache && !empty($cache)) {
            return $cache;
        }

        $entity = new TimelineStatistics($this->client, 'SessionsCount');
        wp_cache_set($cacheName, $entity, 'metricool', MINUTE_IN_SECONDS);

        return $entity;
    }

    /**
     * Returns the visitor statistics for the website during the period for
     * timeline charts usage.
     */
    public function visitors(bool $useCache = true): TimelineStatistics
    {
        $cacheName = 'metricool_entities_cache_statistics_facade_visitors';
        $cache = wp_cache_get($cacheName, 'metricool');
        if ($useCache && !empty($cache)) {
            return $cache;
        }

        $entity = new TimelineStatistics($this->client, 'Visitors');
        wp_cache_set($cacheName, $entity, 'metricool', MINUTE_IN_SECONDS);

        return $entity;
    }

    /**
     * Returns the amount of posts made on the blog during the period for
     * timeline charts usage.
     */
    public function posts(bool $useCache = true): TimelineStatistics
    {
        $cacheName = 'metricool_entities_cache_statistics_facade_posts';
        $cache = wp_cache_get($cacheName, 'metricool');
        if ($useCache && !empty($cache)) {
            return $cache;
        }

        $entity = new TimelineStatistics($this->client, 'DailyPosts');
        wp_cache_set($cacheName, $entity, 'metricool', MINUTE_IN_SECONDS);

        return $entity;
    }

    /**
     * Returns the amount of comments made on the blog during the period for
     * timeline charts usage.
     */
    public function comments(bool $useCache = true): TimelineStatistics
    {
        $cacheName = 'metricool_entities_cache_statistics_facade_comments';
        $cache = wp_cache_get($cacheName, 'metricool');
        if ($useCache && !empty($cache)) {
            return $cache;
        }

        $entity = new TimelineStatistics($this->client, 'DailyComments');
        wp_cache_set($cacheName, $entity, 'metricool', MINUTE_IN_SECONDS);

        return $entity;
    }
}
