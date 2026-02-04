<?php

declare(strict_types=1);

namespace Metricool\Features\Onboarding;

use Metricool\Features\Onboarding\Services\CreateAccountService;
use Metricool\Interfaces\FeatureInterface;

class OnboardingController implements FeatureInterface
{
    private OnboardingService $onboarding;
    private CreateAccountService $accounts;

    public function __construct(OnboardingService $onboarding, CreateAccountService $accounts)
    {
        $this->onboarding = $onboarding;
        $this->accounts = $accounts;
    }

    public function register(): void
    {
        add_filter('metricool_rest_routes', [$this, 'addRoutes']);
    }

    /**
     * Add onboarding routes to the existing routes of our plugin
     */
    public function addRoutes(array $routes): array
    {
        $routes['onboarding/create_account'] = [
            'methods' => 'POST',
            'callback' => [$this, 'createAccount'],
        ];

        $routes['onboarding/finish_onboarding'] = [
            'methods' => 'POST',
            'callback' => [$this, 'finishOnboarding'],
        ];

        $routes['onboarding/retry_onboarding'] = [
            'methods' => 'POST',
            'callback' => [$this, 'retryOnboarding'],
        ];

        return $routes;
    }

    /**
     * Create a new SimplyBook account. This endpoint handles:
     * 1. Validating email and terms acceptance
     * 2. Storing company data
     * 3. Triggering company registration at SimplyBook.me
     */
    public function createAccount(\WP_REST_Request $request): \WP_REST_Response
    {
        // Validate
        $email = $request->get_param('email');
        $password = $request->get_param('password');
        $newsletters = (bool) $request->get_param('newsletters');
        $captcha = $request->get_param('captcha');

        $errors = [];

        if (!is_email($email)) {
            $errors['username'] = __('Email invalid.', 'metricool');
        }

        // todo: do we need to check password length and complexity here?
        // or in the AL?
        // or let the Metricool API handle this?
        if (is_null($password)) {
            $errors['username'] = __('Password is required.', 'metricool');
        }

        if (is_null($captcha)) {
            $errors['captcha'] = __('Captcha is required.', 'metricool');
        }

        if (!empty($errors)) {
            return $this->onboarding->sendHttpErrorResponse(
                __('Validation failed.', 'metricool'),
                ['errors' => $errors],
                422
            );
        }

        try {
            $response = $this->accounts->createAccount([
                'username' => $email,
                'newsletters' => $newsletters
            ], $captcha);
        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
            if ($e instanceof \GuzzleHttp\Exception\RequestException) {
                $response = $e->getResponse();
                return $this->onboarding->sendHttpErrorResponse(
                    __('Failed to create account.', 'metricool'),
                    $response->getBody()->getContents(),
                    $response->getStatusCode()
                );
            }
            return $this->onboarding->sendHttpErrorResponse(
                __('Failed to create account.', 'metricool'),
            );
        }


        return $this->onboarding->sendHttpResponse(
            $response->getData(),
            false,
            '',
            $response->getStatusCode()
        );
    }

    /**
     * Method is used to finish the onboarding process. It is called when the
     * user has completed the onboarding process and wants to finish it.
     */
    public function finishOnboarding(\WP_REST_Request $request): \WP_REST_Response
    {
        $code = 200;
        $message = __('Successfully finished onboarding!', 'metricool');

        $success = $this->onboarding->setOnboardingCompleted();
        if (!$success) {
            $message = __('An error occurred while finishing the onboarding process', 'metricool');
            $code = 500;
        }

        return $this->onboarding->sendHttpResponse([], $success, $message, $code);
    }

    /**
     * Method is used to retry the onboarding process. It is called when the
     * user has completed the onboarding process and wants to retry it.
     */
    public function retryOnboarding(\WP_REST_Request $request): \WP_REST_Response
    {
//        $success = $this->>onboarding->delete_all_options(); // todo
        $success = (bool) random_int(0, 1); // todo
        $message = __('Successfully removed all previous data.', 'metricool');

        if (!$success) {
            $message = __('An error occurred while trying to remove previous data.', 'metricool');
        }

        return $this->onboarding->sendHttpResponse([], $success, $message);
    }
}
