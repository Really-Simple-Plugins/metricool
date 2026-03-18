<?php

namespace Metricool\Features\Onboarding\Services;

use GuzzleHttp\Exception\GuzzleException;
use Metricool\Http\Metricool\MetricoolApi;
use Metricool\Http\RSPAL\RspalApiClient;

class CreateAccountService
{
    private RspalApiClient $rspalClient;
    private MetricoolApi $metricoolApi;

    public function __construct(RspalApiClient $rspalClient, MetricoolApi $metricoolApi)
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
        $signupResponse = $this->rspalClient->signUp([
            'username' => $email,
            'newsletters' => $newsletters,
        ], [
            'RSPAL-RecaptchaV3Token' => $captcha
        ]);

        if (empty($signupResponse->data->accessToken)) {
            return false;
        }

        // Store authentication data
        $this->metricoolApi->authenticate(
            (string) $signupResponse->data->userId,
            (string) $signupResponse->data->accessToken,
            (string) $signupResponse->data->refreshToken,
            $signupResponse->data->expires ?? 300
        );

        // Set the user password
        $this->metricoolApi->userCredentials()
             ->updatePassword('', $password);

        return true;
    }
}
