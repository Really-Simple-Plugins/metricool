<?php

namespace Metricool\Features\Onboarding\Services;

use GuzzleHttp\Exception\GuzzleException;
use Metricool\Http\Metricool\MetricoolApi;
use Metricool\Http\Metricool\MetricoolClient;
use Metricool\Http\RSPAL\RspalApiClient;

class CreateAccountService
{
    private RspalApiClient $rspalClient;
    private MetricoolClient $metricoolClient;
    private MetricoolApi $metricoolApi;

    public function __construct(RspalApiClient $rspalClient, MetricoolClient $metricoolClient, MetricoolApi $metricoolApi)
    {
        $this->rspalClient = $rspalClient;
        $this->metricoolClient = $metricoolClient;
        $this->metricoolApi = $metricoolApi;
    }

    /**
     * Creates a new Metricool account and authenticates the user.
     *
     * @param array{
     *     email: string,
     *     password: string,
     *     newsletters: bool,
     *     captcha: string} $data
     * @throws GuzzleException
     */
    public function createAccount(array $data): bool
    {
        $signupData = [
            'username' => $data['email'],
            'newsletters' => $data['newsletters'],
        ];

        $signupResponse = $this->rspalClient->signUp($signupData, [
            'RSPAL-RecaptchaV3Token' => $data['captcha']
        ]);

        // Authenticate the user
        $this->metricoolClient->authenticate(
            $signupResponse->data->userId,
            $signupResponse->data->accessToken,
            $signupResponse->data->refreshToken
        );

        // Update the user password
        $this->metricoolApi->userCredentials()->patch('', $data['password']);

        // Return true to indicate a successful signup
        return true;
    }
}
