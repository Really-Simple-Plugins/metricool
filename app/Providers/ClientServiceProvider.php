<?php

declare(strict_types=1);

namespace Metricool\Providers;

use Metricool\Http\Metricool\MetricoolClient;
use Metricool\Http\Metricool\MetricoolApi;

// todo - should we just bind MetricoolApi instead of wrapping it in a provider?
// We can do that by requiring it as a dependency in the constructor of classes that need it.
class ClientServiceProvider extends Provider
{
    protected array $provides = [
        'client',
    ];

    /**
     * Provides the API client for the application to use
     * @example $this->app->client || $this->app->get('client')
     */
    public function provideClient(): MetricoolApi
    {
        $client = new MetricoolClient();

        if (defined('METRICOOL_BLOG_ID') && !empty(METRICOOL_BLOG_ID)) {
            $client->setBlogId(METRICOOL_BLOG_ID); // todo - fetch from settings
        }

        if (defined('METRICOOL_USER_ID') && !empty(METRICOOL_USER_ID)) {
            $client->setUserId(METRICOOL_USER_ID); // todo - fetch from settings
        }

        if (defined('METRICOOL_USER_TOKEN') && !empty(METRICOOL_USER_TOKEN)) {
            $client->setUserToken(METRICOOL_USER_TOKEN); // todo - fetch from settings
        }

        $env = defined('METRICOOL_ENV') ? METRICOOL_ENV : 'production';
        if ($env !== 'production') {
            $client->setTesting(true);
        }

        try {
            $client->connect();
            return new MetricoolApi($client);
        } catch (\Exception $e) {
            throw new \RuntimeException('Failed to setup the Metricool API in the container: ' . $e->getMessage());
        }
    }
}
