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
    private OAuthService $oauth;

    public function __construct(RspalApiClient $rspalClient, MetricoolApi $metricoolApi, OnboardingService $onboarding, OAuthService $oauth)
    {
        $this->rspalClient = $rspalClient;
        $this->metricoolApi = $metricoolApi;
        $this->onboarding = $onboarding;
        $this->oauth = $oauth;
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
        } catch (GuzzleException $e) {
            throw new CreateAccountException(__('Something went wrong.', 'metricool'), $e->getMessage(), 500);
        }

        // Check if the user already exists
        if ($signupResponse->getStatusCode() == 400) {
            throw new CreateAccountException(__('E-mail address exists.', 'metricool'), json_encode($signupResponse), 422);
        }

        // Check if the response contains the required fields
        if (empty($signupResponse->data->accessToken) || empty($signupResponse->data->refreshToken)) {
            throw new CreateAccountException(__('Something went wrong.', 'metricool'), 'Signup response is missing required fields', 500);
        }

        // Parse the user ID from the access token
        $userId = $this->oauth->parseUserIdFromAccessToken($signupResponse->data->accessToken);

        // Store authentication data
        $this->metricoolApi->authenticate(
            $userId,
            (string) $signupResponse->data->accessToken,
            (string) $signupResponse->data->refreshToken,
            $signupResponse->data->expires ?? 300
        );

        // Attempt to set the password
        $this->updatePassword($password);

        // Attempt to automatically set the blog information, complete the onboarding process on success
        if ($this->onboarding->findAndRetrieveBlogInfo()) {
            $this->onboarding->setOnboardingCompleted();
            $this->onboarding->setShowWelcomeScreen();
        }

        return true;
    }

    /**
     * Update the user's password.
     */
    protected function updatePassword(string $password): void
    {
        try {
            $this->metricoolApi->userCredentials()
                ->updatePassword('', $password);
        } catch (GuzzleException $e) {
            // todo: When this happens, the user account is created but the password is not set, so the user can't log in. We should handle this case properly,
            // maybe by deleting the created account or allowing the user to set the password later?
            $this->metricoolApi->unAuthenticate();

            throw new CreateAccountException(__('Something went wrong', 'metricool'), $e->getMessage(), 500);
        }
    }
}
