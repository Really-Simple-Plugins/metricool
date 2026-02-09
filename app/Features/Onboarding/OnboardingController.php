<?php

declare(strict_types=1);

namespace Metricool\Features\Onboarding;

use Metricool\Features\Onboarding\Services\CreateAccountService;
use Metricool\Http\Metricool\MetricoolApi;
use Metricool\Interfaces\FeatureInterface;

class OnboardingController implements FeatureInterface
{
    private OnboardingService $onboarding;
    private CreateAccountService $accounts;
    private MetricoolApi $api;

    public function __construct(OnboardingService $onboarding, CreateAccountService $accounts, MetricoolApi $api)
    {
        $this->onboarding = $onboarding;
        $this->accounts = $accounts;
        $this->api = $api;
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
     * Create a new Metricool account. The created user is authenticated
     * automatically.
     */
    public function createAccount(\WP_REST_Request $request): \WP_REST_Response
    {
        // todo: storage ?
        $email = (string) $request->get_param('email');
        $password = (string) $request->get_param('password');
        $newsletters = (bool) $request->get_param('newsletters');
        $captcha = (string) $request->get_param('captcha');

        // Validate fields
        if (!is_email($email) || empty($password) || empty($captcha)) {
            return $this->onboarding->sendHttpErrorResponse(
                __('Validation failed.', 'metricool'),
                [],
                422
            );
        }

        // Attempt to create the account
        try {
            $createAccount = $this->accounts->createAccount([
                'email' => $email,
                'newsletters' => $newsletters,
                'password' => $password,
                'captcha' => $captcha,
            ]);

            // Account created successfully and the user is authenticated
            return $this->onboarding->sendHttpResponse(
                $createAccount,
                true,
                __('Account created successfully.', 'metricool')
            );
        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
            if ($e instanceof \GuzzleHttp\Exception\RequestException) {

                // If the error contains a response, return it
                $response = $e->getResponse();

                return $this->onboarding->sendHttpErrorResponse(
                    __('Failed to create account.', 'metricool'),
                    ['error' => $response->getBody()->getContents()],
                    $response->getStatusCode()
                );
            }

            // Return a connection error on every other exception
            return $this->onboarding->sendHttpErrorResponse(
                __('Failed to connect.', 'metricool'),
                ['error' => $e->getMessage()]
            );
        }
    }

    /**
     * Method is used to finish the onboarding process. It is called when the
     * user has completed the onboarding process and wants to finish it.
     */
    public function finishOnboarding(\WP_REST_Request $request): \WP_REST_Response
    {
        if (!$this->api->hasAuthentication()) {
            return $this->onboarding->sendHttpErrorResponse('Onboarding failed. User is not authenticated.');
        }

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
