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

    public function login(string $email, string $password): bool
    {
        // Authenticate with mock-up credentials
        // Todo: remove mock-up
        $this->metricoolApi->authenticate(
            METRICOOL_USER_ID,
            METRICOOL_USER_TOKEN,
            'test_refresh_token'
        );

        return true;
    }
}
