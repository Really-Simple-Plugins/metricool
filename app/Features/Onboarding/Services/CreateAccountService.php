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
//        $this->rspalClient->signUp([
//            'username' => $email,
//            'newsletters' => $newsletters,
//        ], [
//            'RSPAL-RecaptchaV3Token' => $captcha
//        ]);

        // Authenticate with mock-up credentials
        // Todo: remove mock-up
        $this->metricoolApi->authenticate(
            METRICOOL_USER_ID,
            METRICOOL_USER_TOKEN,
            'test_refresh_token'
        );

        // Authenticate and set the password
        // $this->metricoolApi->authenticate(
        //     $signupResponse->data->userId,
        //     $signupResponse->data->accessToken,
        //     $signupResponse->data->refreshToken
        // );

        // $this->metricoolApi->userCredentials()
        //     ->updatePassword('', $password);

        return true;
    }
}
