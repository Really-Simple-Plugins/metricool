<?php
namespace Metricool\Providers;

use Metricool\Helpers\Request;
use Metricool\Http\Metricool\MetricoolClient;
use Metricool\Http\Metricool\MetricoolEntities;

class AppServiceProvider extends Provider
{
    protected array $provides = [
        'request',
        'client',
    ];

    /**
     * Provides the global request object for the application to use
     * @example App::provide('request')
     */
    public function provideRequest(): Request
    {
        return Request::fromGlobal();
    }

    /**
     * Provides the API client for the application to use
     * @throws \RuntimeException Informative for other developers.
     * @example App::provide('client')->connectedAccounts()->list();
     */
    public function provideClient(): MetricoolEntities
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
            return new MetricoolEntities($client);
        } catch (\Exception $e) {
            throw new \RuntimeException('Failed to setup the Metricool API in the container: '.$e->getMessage());
        }
    }
}