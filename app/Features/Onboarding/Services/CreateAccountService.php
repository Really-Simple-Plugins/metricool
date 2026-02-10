<?php

namespace Metricool\Features\Onboarding\Services;

use GuzzleHttp\Exception\GuzzleException;
use Metricool\Features\Onboarding\OnboardingService;
use Metricool\Http\Metricool\MetricoolApi;
use Metricool\Http\RSPAL\RspalApiClient;

class CreateAccountService
{
    private RspalApiClient $rspalClient;
    private MetricoolApi $metricoolApi;

    public function __construct(RspalApiClient $rspalClient, MetricoolApi $metricoolApi, OnboardingService $service)
    {
        $this->rspalClient = $rspalClient;
        $this->metricoolApi = $metricoolApi;
    }

    /**
     * Creates a new Metricool account and authenticate the MetricoolClient.
     * @throws GuzzleException
     */
    public function createAccount(string $captcha, string $email, string $password, bool $newsletters): bool
    {
        // Attempt to sign up the user through RSPAL
        $this->rspalClient->signUp([
            'username' => $email,
            'newsletters' => $newsletters,
        ], [
            'RSPAL-RecaptchaV3Token' => $captcha
        ]);

        // Todo: remove mock-up
        if (true) {
            // Authenticate with test credentials
            $this->metricoolApi->authenticate(
                '3864308',
                'RCGXYAHRFQXWRXODYNGCBUMHKTSQRDJQSWWLXDCCBIKHHDEAOLQJAGEDQBPIZINX',
                'test_refresh_token'
            );
        } else {
            // Authenticate and set the password
            $this->metricoolApi->authenticate(
                $signupResponse->data->userId,
                $signupResponse->data->accessToken,
                $signupResponse->data->refreshToken
            );

            $this->metricoolApi->userCredentials()
                ->updatePassword('', $password);
        }

        return true;
    }
}
