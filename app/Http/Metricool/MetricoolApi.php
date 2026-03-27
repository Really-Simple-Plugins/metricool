<?php

declare(strict_types=1);

namespace Metricool\Http\Metricool;

/**
 * Facade for Metricool API entities. Calls to undefined methods are routed to
 * {@see MetricoolClient}
 *
 * @method bool hasAuthentication()
 * @method bool authenticate(string $userId, string $userToken, string $refreshToken, $expires)
 * @method bool hasUserToken()
 * @method bool getUserId()
 * @method bool hasUserId()
 * @method bool getBlogId()
 * @method bool hasBlogId()
 * @method bool storeBlogId(string $blogId)
 * @method bool isTokenExpired()
 * @method bool isTesting()
 * @method bool isConnected()
 * @method bool logout()
 * @method array exchangeOAuthCode(string $code, string $redirectUri)
 * @method void refreshAuthToken()
 */
class MetricoolApi
{
    protected ?MetricoolClient $client = null;

    public function __construct(MetricoolClient $client)
    {
        $this->client = $client;
    }

    /**
     * Easy access to the ConnectedBrands entity.
     */
    public function connectedBrands(bool $useCache = true): Entities\ConnectedBrands
    {
        $cacheName = 'metricool_entities_cache_connected_brands';
        $cache = wp_cache_get($cacheName, 'metricool');
        if ($useCache && !empty($cache)) {
            return $cache;
        }

        $entity = new Entities\ConnectedBrands($this->client);
        wp_cache_set($cacheName, $entity, 'metricool', MINUTE_IN_SECONDS);

        return $entity;
    }

    /**
     * Easy access to the Subscription entity.
     */
    public function subscription(bool $useCache = true): Entities\Subscription
    {
        $cacheName = 'metricool_entities_cache_subscription';
        $cache = wp_cache_get($cacheName, 'metricool');
        if ($useCache && !empty($cache)) {
            return $cache;
        }

        $entity = new Entities\Subscription($this->client);
        wp_cache_set($cacheName, $entity, 'metricool', MINUTE_IN_SECONDS);

        return $entity;
    }

    /**
     * Easy access to the statistic entities via the StatisticsFacade.
     */
    public function statistics(bool $useCache = true): Entities\Facades\StatisticsFacade
    {
        $cacheName = 'metricool_entities_cache_statistics_facade';
        $cache = wp_cache_get($cacheName, 'metricool');
        if ($useCache && !empty($cache)) {
            return $cache;
        }

        $entity = new Entities\Facades\StatisticsFacade($this->client);
        wp_cache_set($cacheName, $entity, 'metricool', MINUTE_IN_SECONDS);

        return $entity;
    }

    /**
     * Easy access to the real time entities via the RealtimeFacade.
     */
    public function realtime(bool $useCache = true): Entities\Facades\RealtimeFacade
    {
        $cacheName = 'metricool_entities_cache_realtime_facade';
        $cache = wp_cache_get($cacheName, 'metricool');
        if ($useCache && !empty($cache)) {
            return $cache;
        }

        $entity = new Entities\Facades\RealtimeFacade($this->client);
        wp_cache_set($cacheName, $entity, 'metricool', MINUTE_IN_SECONDS);

        return $entity;
    }

    /**
     * Easy access to the UserSettings entity.
     */
    public function userSettings(bool $useCache = true): Entities\UserSettings
    {
        $cacheName = 'metricool_entities_cache_user_settings';
        $cache = wp_cache_get($cacheName, 'metricool');
        if ($useCache && !empty($cache)) {
            return $cache;
        }

        $entity = new Entities\UserSettings($this->client);
        wp_cache_set($cacheName, $entity, 'metricool', MINUTE_IN_SECONDS);

        return $entity;
    }

    /**
     * Easy access to the UpdatePassword entity.
     */
    public function userCredentials(): Entities\UserCredentials
    {
        return new Entities\UserCredentials($this->client);
    }

    /**
     * Easy access to the ConnectedBrands entity.
     */
    public function brands(bool $useCache = true): Entities\Brands
    {
        $cacheName = 'metricool_entities_cache_user_brands';
        $cache = wp_cache_get($cacheName, 'metricool');
        if ($useCache && !empty($cache)) {
            return $cache;
        }

        $entity = new Entities\Brands($this->client);
        wp_cache_set($cacheName, $entity, 'metricool', MINUTE_IN_SECONDS);

        return $entity;
    }

    /**
     * This magic method is called when a method is requested that does not
     * exist on this class. It will try to call the method on the
     * MetricoolClient instance, if not found, it will throw an exception.
     *
     * @return mixed
     * @throws \BadMethodCallException
     */
    public function __call(string $name, array $arguments)
    {
        if (method_exists($this->client, $name)) {
            return $this->client->{$name}(...$arguments);
        }

        throw new \BadMethodCallException("Method {$name} does not exist on MetricoolClient.");
    }
}
