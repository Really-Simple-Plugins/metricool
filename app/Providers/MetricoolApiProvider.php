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
        /** @var MetricoolClient $client */
        $client = App::getInstance()->make(MetricoolClient::class);

        if ($blogId = get_option('metricool_blog_id')) {
            $client->setBlogId($blogId);
        }

        if ($userId = get_option('metricool_user_id')) {
            $client->setUserId($userId);
        }

        if ($userToken = get_option('metricool_auth_token')) {
            $client->setUserToken($userToken);
        }

        try {
            $client->connect();
        } catch (\Exception $e) {
            throw new \RuntimeException('Failed to setup the Metricool API in the container: ' . esc_html($e->getMessage()));
        }

        // Refresh the token if it's expired
        if ($client->hasAuthentication() && $client->isTokenExpired()) {
            try {
                $client->refreshAuthToken();
            } catch (\RuntimeException $e) {
                // maybe show an error that the user is logged out?
            }
        }

        return new MetricoolApi($client);
    }
}
