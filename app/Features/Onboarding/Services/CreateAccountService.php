<?php

namespace Metricool\Features\Onboarding\Services;

use GuzzleHttp\Exception\GuzzleException;
use Metricool\Features\Onboarding\Exceptions\CreateAccountException;
use Metricool\Features\Onboarding\OnboardingService;
use Metricool\Http\Metricool\MetricoolApi;
use Metricool\Http\RSPAL\RspalApiClient;
use Metricool\Services\DashboardService;

class CreateAccountService
{
    private RspalApiClient $rspal;
    private MetricoolApi $api;
    private OnboardingService $onboarding;
    private OAuthService $oauth;
    private DashboardService $dashboard;

    public function __construct(
        RspalApiClient $rspal,
        MetricoolApi $api,
        OnboardingService $onboarding,
        OAuthService $oauth,
        DashboardService $dashboard
    ) {
        $this->rspal = $rspal;
        $this->api = $api;
        $this->onboarding = $onboarding;
        $this->oauth = $oauth;
        $this->dashboard = $dashboard;
    }

    /**
     * Creates a new Metricool account and authenticate the MetricoolClient.
     * @throws CreateAccountException
     */
    public function createAccount(string $captcha, string $email, string $password, bool $newsletters): bool
    {
        // First, check if the password is valid
        if (!$this->isValidPassword($password)) {
            throw new CreateAccountException(__('Password is not valid.', 'metricool'), 'Password is not valid', 422);
        }

        // Attempt to create the account
        try {
            $signupResponse = $this->rspal->signUp([
                'username' => $email,
                'newsletters' => $newsletters,
            ], [
                'RSPAL-RecaptchaV3Token' => $captcha
            ]);
        } catch (GuzzleException $e) {
            throw new CreateAccountException(__('Something went wrong.', 'metricool'), $e->getMessage(), 500);
        }

        // A 400 response means e-mail exists or the password is invalid
        if ($signupResponse->getStatusCode() == 400) {
            throw new CreateAccountException(__('Something went wrong.', 'metricool'), 'Email or password error', 422);
        }

        // Check if the response contains the required fields
        if (empty($signupResponse->data->accessToken) || empty($signupResponse->data->refreshToken)) {
            throw new CreateAccountException(__('Something went wrong.', 'metricool'), 'Signup response is missing required fields', 500);
        }

        // Parse the user ID from the access token
        $userId = $this->oauth->parseUserIdFromAccessToken($signupResponse->data->accessToken);

        if (empty($userId)) {
            throw new CreateAccountException(__('Something went wrong.', 'metricool'), 'Failed to parse user ID from access token', 500);
        }

        // Authenticate the Metricool API Client
        $this->api->authenticate(
            $userId,
            (string) $signupResponse->data->accessToken,
            (string) $signupResponse->data->refreshToken,
            $signupResponse->data->expires ?? 300
        );

        try {
            $this->api->userCredentials()
                ->updatePassword('', $password);
        } catch (GuzzleException $e) {
            // todo: When this happens, the user account is created but the password is not set, so the user can't log in. We should handle this case properly,
            // maybe by deleting the created account or allowing the user to set the password later?
            $this->api->unAuthenticate();

            throw new CreateAccountException(__('Something went wrong', 'metricool'), $e->getMessage(), 500);
        }

        // Attempt to automatically set the blog information, complete the onboarding process on success
        if ($this->onboarding->finalizeOnboarding()) {
            $this->dashboard->setShowWelcomeScreen();
        }

        return true;
    }

    /**
     * Check if the password is: between 8 and 20 characters long, contains at least one uppercase letter, one lowercase letter, one number, and one special character
     */
    protected function isValidPassword(string $password): bool
    {
        return (bool) preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^a-zA-Z\d]).{8,20}$/', $password);
    }
}
