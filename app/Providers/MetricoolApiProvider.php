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

        if ($blogId = get_option('metricool_blog_id')) {
            $client->setBlogId($blogId); // todo - fetch from settings
        }

        if ($userId = get_option('metricool_user_id')) {
            $client->setUserId($userId);
        }

        if ($userToken = get_option('metricool_auth_token')) {
            $client->setUserToken($userToken);
        }

        try {
            $client->connect();
            return new MetricoolApi($client);
        } catch (\Exception $e) {
            throw new \RuntimeException('Failed to setup the Metricool API in the container: ' . $e->getMessage());
        }
    }
}
