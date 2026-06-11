<?php

declare(strict_types=1);

namespace Metricool\Http\Metricool;

/**
 * Facade for Metricool API entities. Calls to undefined methods are routed to
 * {@see MetricoolClient}
 *
 * @mixin MetricoolClient
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
    public function user(): Entities\User
    {
        return new Entities\User($this->client);
    }

    /**
     * Easy access to the UserSettings entity.
     */
    public function userSettings(): Entities\UserSettings
    {
        return new Entities\UserSettings($this->client);
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
     * List of languages that Metricool supports
     */
    public static function supportedLanguages(): array
    {
        return [
            [
                'label' => __('English', 'metricool'),
                'value' => 'en',
            ],
            [
                'label' => __('Spanish', 'metricool'),
                'value' => 'es',
            ],
            [
                'label' => __('French', 'metricool'),
                'value' => 'fr',
            ],
            [
                'label' => __('Portuguese (Portugal)', 'metricool'),
                'value' => 'pt',
            ],
            [
                'label' => __('Portuguese (Brazil)', 'metricool'),
                'value' => 'br',
            ],
            [
                'label' => __('Deutsch', 'metricool'),
                'value' => 'de',
            ],
            [
                'label' => __('Italian', 'metricool'),
                'value' => 'it',
            ],
            [
                'label' => __('Danish', 'metricool'),
                'value' => 'da',
            ],
            [
                'label' => __('Dutch', 'metricool'),
                'value' => 'nl',
            ],
        ];
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

        throw new \BadMethodCallException(esc_html("Method {$name} does not exist on MetricoolClient."));
    }
}
