<?php

namespace Metricool\Features\Onboarding\Services;

use Metricool\Http\Metricool\MetricoolApi;

class AuthService
{
    private MetricoolApi $metricoolApi;

    public function __construct(MetricoolApi $metricoolApi)
    {
        $this->metricoolApi = $metricoolApi;
    }

    public function login(string $email, string $password)
    {
        // Authenticate with mock-up credentials
        // Todo: remove mock-up
        $this->metricoolApi->authenticate(
            '3864308',
            'RCGXYAHRFQXWRXODYNGCBUMHKTSQRDJQSWWLXDCCBIKHHDEAOLQJAGEDQBPIZINX',
            'test_refresh_token'
        );

        return true;
    }
}