<?php

declare(strict_types=1);

namespace Metricool\Providers;

use Metricool\Bootstrap\App;
use Metricool\Http\RSPAL\RspalApiClient;

class RspalApiProvider extends Provider
{
    /**
     * Provides the API client for the application to use
     * Example: $this->app->get(MetricoolApi::clas)
     * Example DI: public function __construct(MetricoolApi $client) { ... }
     */
    public static function provideClientSingleton(): RspalApiClient
    {
        return App::getInstance()->make(RspalApiClient::class);
    }
}
