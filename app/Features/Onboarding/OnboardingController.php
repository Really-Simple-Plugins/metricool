<?php

declare(strict_types=1);

namespace Metricool\Features\Onboarding;

use GuzzleHttp\Exception\GuzzleException;
use Metricool\Features\Onboarding\Exceptions\BrandAccessDeniedException;
use Metricool\Features\Onboarding\Services\AuthService;
use Metricool\Features\Onboarding\Services\CreateAccountService;
use Metricool\Http\Metricool\MetricoolApi;
use Metricool\Interfaces\FeatureInterface;
use Metricool\Services\TrackingScriptService;
use Metricool\Traits\HasRestAccess;

class OnboardingController implements FeatureInterface
{
    use HasRestAccess;

    private MetricoolApi $api;
    private OnboardingService $onboarding;
    private CreateAccountService $accounts;
    private AuthService $auth;

    public function __construct(MetricoolApi $api, OnboardingService $onboarding, CreateAccountService $accounts, AuthService $auth, TrackingScriptService $tracking)
    {
        $this->api = $api;
        $this->onboarding = $onboarding;
        $this->accounts = $accounts;
        $this->auth = $auth;
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
        $routes['onboarding/login'] = [
            'methods' => 'GET',
            'callback' => [$this, 'login'],
        ];

        $routes['onboarding/create_account'] = [
            'methods' => 'POST',
            'callback' => [$this, 'createAccount'],
        ];

        $routes['onboarding/finish_onboarding'] = [
            'methods' => 'POST',
            'callback' => [$this, 'finishOnboarding'],
        ];

        return $routes;
    }

    /**
     * Handle incoming login request.
     *
     */
    public function login(\WP_REST_Request $request): \WP_REST_Response
    {
        return $this->sendHttpResponse([
            'onboarding' => $this->onboarding->state(),
        ]);
    }

    /**
     * Create a new Metricool account. The created user is authenticated
     * automatically.
     *
     * @throws \GuzzleHttp\Exception\GuzzleException when Metricool API request fails
     */
    public function createAccount(\WP_REST_Request $request): \WP_REST_Response
    {
        // todo: storage ?
        $email = (string) $request->get_param('email');
        $password = (string) $request->get_param('password');
        $marketing = (bool) $request->get_param('marketing');
        $captcha = (string) $request->get_param('captcha');
        $terms = (bool) $request->get_param('terms');

        // Validate fields
        if (!is_email($email) || empty($password) || empty($captcha) || !$terms) {
            return $this->sendHttpErrorResponse(
                __('Validation failed.', 'metricool'),
                [],
                422
            );
        }

        // Attempt to create the account
        try {
            $accountCreated = $this->accounts->createAccount($captcha, $email, $password, $marketing);

            if (!$accountCreated) {
                return $this->sendHttpErrorResponse(__('Could not create account. Please try again later.'), ['error' => 'No accessToken in response']);
            }
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            // Catch RequestException because it could be an email exists error
            // Todo: add a better way to handle this because it's not very clean'
            $response = $e->getResponse();
            $error = json_decode($response->getBody()->getContents());

            // Metricool return a 400 response with the same body on validation errors and existing e-mail addresses
            // Because we already did the validation, we can assume that it's an e-mail exists error
            $message = $response->getStatusCode() == 400
                ? __('E-mail already exists', 'metricool')
                : __('Unknown Error. Please try again later.', 'metricool');

            return $this->sendHttpErrorResponse(
                $message,
                ['error' => $error],
                $response->getStatusCode()
            );
        }

        try {
            $brands = $this->api->brands()->all();
        } catch (GuzzleException $e) {
            // todo: if this happens, the user could be stuck in the onboarding process, maybe we should unauthenticate and logout
            return $this->sendHttpErrorResponse('Error while retrieving brands.');
        }

        // Attempt to automatically set the blog information
        if ($this->onboarding->findAndRetrieveBlogInfo($brands)) {
            $this->onboarding->setOnboardingCompleted();
        }

        return $this->sendHttpResponse([
            'onboarding' => $this->onboarding->state()
        ]);
    }

    /**
     * Method is used to finish the onboarding process. It is called when the
     * user has completed the onboarding process and wants to finish it.
     *
     * @throws GuzzleException
     */
    public function finishOnboarding(\WP_REST_Request $request): \WP_REST_Response
    {
        $blogId = (string) $request->get_param('blog_id');

        // Store the blogId if it was provided by the client, to store the necessary blog information
        if (!empty($blogId)) {
            try {
                $this->onboarding->storeBlogInfo($blogId);
            } catch (BrandAccessDeniedException $e) {
                return $this->sendHttpErrorResponse(__('Brand Access denied.', 'metricool'), [], 403);
            }
        }

        // Check if the necessary authentication data is present
        if (!$this->api->hasAuthentication()) {
            return $this->sendHttpErrorResponse('Onboarding failed. User is not authenticated.');
        }

        return $this->sendHttpResponse([
            'onboarding' => $this->onboarding->state()
        ]);
    }
}
