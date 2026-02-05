<?php

declare(strict_types=1);

namespace Metricool\Providers;

use Metricool\Bootstrap\App;
use Metricool\Http\Metricool\MetricoolApi;
use Metricool\Http\Metricool\MetricoolClient;


class MetricoolApiProvider extends Provider
{
    /**
     * @inheritDoc
     */
    protected array $singletons = [
        'client' => MetricoolApi::class,
    ];

    /**
     * Provides the API client for the application to use
     * Example: $this->app->get(MetricoolApi::clas)
     * Example DI: public function __construct(MetricoolApi $client) { ... }
     */
    public static function provideClientSingleton(): MetricoolApi
    {
        $client = App::getInstance()->make(MetricoolClient::class);

        if (defined('METRICOOL_BLOG_ID') && !empty(METRICOOL_BLOG_ID)) {
            $client->setBlogId(METRICOOL_BLOG_ID); // todo - fetch from settings
        }

        if (defined('METRICOOL_USER_ID') && !empty(METRICOOL_USER_ID)) {
            $client->setUserId(METRICOOL_USER_ID); // todo - fetch from settings
        }

        if (defined('METRICOOL_USER_TOKEN') && !empty(METRICOOL_USER_TOKEN)) {
            $client->setUserToken(METRICOOL_USER_TOKEN); // todo - fetch from settings
        }

        try {
            $client->connect();
            return new MetricoolApi($client);
        } catch (\Exception $e) {
            throw new \RuntimeException('Failed to setup the Metricool API in the container: ' . $e->getMessage());
        }
    }
}
