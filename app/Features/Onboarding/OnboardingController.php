<?php

declare(strict_types=1);

namespace Metricool\Features\Onboarding;

use Metricool\Features\Onboarding\Services\AuthService;
use Metricool\Features\Onboarding\Services\CreateAccountService;
use Metricool\Http\Metricool\MetricoolApi;
use Metricool\Interfaces\FeatureInterface;
use Metricool\Services\TrackingScriptService;

class OnboardingController implements FeatureInterface
{
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
        $this->tracking = $tracking;
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
            'methods' => 'POST',
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

        $routes['onboarding/retry_onboarding'] = [
            'methods' => 'POST',
            'callback' => [$this, 'retryOnboarding'],
        ];

        return $routes;
    }

    public function login(\WP_REST_Request $request): \WP_REST_Response
    {
        // todo: storage ?
        $email = (string) $request->get_param('email');
        $password = (string) $request->get_param('password');

        // Validate fields
        if (!is_email($email) || empty($password)) {
            return $this->onboarding->sendHttpErrorResponse(
                __('Validation failed.', 'metricool'),
                [],
                422
            );
        }

        // Attempt to login
        try {
            $this->auth->login($email, $password);
        } catch (\Exception $e) {
            return $this->onboarding->sendHttpErrorResponse(
                __('The combination of username and password was incorrect.', 'metricool'),
                [],
                401
            );
        }

        // Todo: remove mock-up
        $brands = [
            [
                'id' => 4962983,
                'label' => 'Really Simple Plugins',
                'title' => 'https://wimenbente.nl',
                'image' => 'https://static.metricool.com/brand-logo/202507/4962983-file-4477890870715557446.png'
            ],
            [
                'id' => 2221200,
                'label' => 'TestingMetri-Business',
                'title' => 'Metricool',
                'image' => 'https://static.metricool.com/brand-logo/202511/2221200-file-6884100583778344266.jpeg'
            ]
        ];

        // $brands = $this->api->brands()->all();

        // Attempt to automatically set the blog information
        try {
            $blogInfoSet = $this->onboarding->findBlogAndStore($brands);
        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
            $blogInfoSet = false;
        }

        if (!$blogInfoSet) {
            // Return the brands that were found when blogId could not be automatically set, so the user can select one
            return $this->onboarding->sendHttpResponse([
                'onboarding_finished' => false,
                'connected_brands' => $brands,
            ]);
        }

        // Finish onboarding
        $this->onboarding->setOnboardingCompleted();

        return $this->onboarding->sendHttpResponse([
            'onboarding_finished' => true,
        ]);
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
        $marketing = (bool) $request->get_param('marketing');
        $captcha = (string) $request->get_param('captcha');
        $terms = (bool) $request->get_param('terms');

        // Validate fields
        if (!is_email($email) || empty($password) || empty($captcha) || !$terms) {
            return $this->onboarding->sendHttpErrorResponse(
                __('Validation failed.', 'metricool'),
                [],
                422
            );
        }

        // Attempt to create the account
        try {
            $this->accounts->createAccount($captcha, $email, $password, $marketing);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $response = $e->getResponse();

            // Metricool return a 400 response with the same body on validation errors and existing e-mail addresses
            // Because we already did the validation, we can assume that it's an e-mail address already exists error
            $message = $response->getStatusCode() == 400
                ? __('E-mail already exists', 'metricool')
                : __('Unknown Error. Please try again later.', 'metricool');

            return $this->onboarding->sendHttpErrorResponse(
                $message,
                [],
                $response->getStatusCode()
            );
        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
            // Return a connection error on every other exception
            return $this->onboarding->sendHttpErrorResponse(
                __('Failed to connect.', 'metricool'),
                ['error' => $e->getMessage()]
            );
        }

        // Return a mock-up of the brands
        // Todo: remove mock-up
        $brands = [
            [
                'id' => 4962983,
                'label' => 'Really Simple Plugins',
                'title' => 'https://wimenbente.nl',
                'image' => 'https://static.metricool.com/brand-logo/202507/4962983-file-4477890870715557446.png',
                'hash' => '3ea6c275fdc13308a612fe1b4330261b',
            ],
            [
                'id' => 2221200,
                'label' => 'TestingMetri-Business',
                'title' => 'Metricool',
                'image' => 'https://static.metricool.com/brand-logo/202511/2221200-file-6884100583778344266.jpeg',
                'hash' => 'b004950c87f5ffe7de25161216a4c8e4',
            ]
        ];

        try {
            $blogInfoSet = $this->onboarding->findBlogAndStore($brands);
        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
            $blogInfoSet = false;
        }

        if (!$blogInfoSet) {
            // Return the brands that were found when blogId could not be automatically set, so the user can select one
            return $this->onboarding->sendHttpResponse([
                'message' => __('Please select a blog to connect to Metricool.', 'metricool'),
                'finish_onboarding' => false,
                'connected_brands' => $brands,
            ]);
        }

        return $this->onboarding->sendHttpResponse([
            'message' => __('Account created successfully.', 'metricool'),
            'finish_onboarding' => true,
        ]);
    }

    /**
     * Method is used to finish the onboarding process. It is called when the
     * user has completed the onboarding process and wants to finish it.
     */
    public function finishOnboarding(\WP_REST_Request $request): \WP_REST_Response
    {
        $blogId = (string) $request->get_param('blog_id');

        // Store the blogId if it was provided by the client, to store the necessary blog information
        if (!empty($blogId)) {
            try {
                $this->onboarding->storeBlogInfo($blogId);
            } catch (\GuzzleHttp\Exception\GuzzleException $e) {
                return $this->onboarding->sendHttpErrorResponse('Onboarding failed. Failed to retrieve brand information.', [], 502);
            }
        }

        // Check if the necessary authentication data is present
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
