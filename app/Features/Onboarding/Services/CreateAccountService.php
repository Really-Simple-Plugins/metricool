<?php

namespace Metricool\Features\Onboarding\Services;

use GuzzleHttp\Exception\GuzzleException;
use Metricool\Features\Onboarding\Exceptions\CreateAccountException;
use Metricool\Features\Onboarding\OnboardingService;
use Metricool\Http\Metricool\MetricoolApi;
use Metricool\Http\RSPAL\RspalApiClient;

class CreateAccountService
{
    private RspalApiClient $rspalClient;
    private MetricoolApi $metricoolApi;
    private OnboardingService $onboarding;

    public function __construct(RspalApiClient $rspalClient, MetricoolApi $metricoolApi, OnboardingService $onboarding)
    {
        $this->rspalClient = $rspalClient;
        $this->metricoolApi = $metricoolApi;
        $this->onboarding = $onboarding;
    }

    /**
     * Creates a new Metricool account and authenticate the MetricoolClient.
     * @throws CreateAccountException
     */
    public function createAccount(string $captcha, string $email, string $password, bool $newsletters): bool
    {
        try {
            // Attempt to sign up the user through RSPAL
            $signupResponse = $this->rspalClient->signUp([
                'username' => $email,
                'newsletters' => $newsletters,
            ], [
                'RSPAL-RecaptchaV3Token' => $captcha
            ]);

            if ($signupResponse->getStatusCode() == 400) {
                throw new CreateAccountException(__('E-mail address exists.', 'metricool'), json_encode($signupResponse));
            }

            if (empty($signupResponse->data->accessToken) || empty($signupResponse->data->userId) || empty($signupResponse->data->refreshToken)) {
                throw new CreateAccountException(__('Something went wrong.', 'metricool'), 'Signup response is missing required fields');
            }

            // Store authentication data
            $this->metricoolApi->authenticate(
                (string) $signupResponse->data->userId,
                (string) $signupResponse->data->accessToken,
                (string) $signupResponse->data->refreshToken,
                $signupResponse->data->expires ?? 300
            );

            // Attempt to set the password
            try {
                $this->metricoolApi->userCredentials()
                    ->updatePassword('', $password);
            } catch (GuzzleException $e) {
                $this->metricoolApi->unAuthenticate();
                throw new CreateAccountException(__('Something went wrong', 'metricool'), $e->getMessage());
            }

            // Attempt to automatically set the blog information
            if ($this->onboarding->findAndRetrieveBlogInfo()) {
                $this->onboarding->setOnboardingCompleted();
                $this->onboarding->setShowWelcomeScreen();
            }
        } catch (GuzzleException $e) {
            throw new CreateAccountException(__('Something went wrong.', 'metricool'), $e->getMessage());
        }

        return true;
    }
}
