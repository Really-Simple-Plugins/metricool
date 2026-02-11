<?php

declare(strict_types=1);

namespace Metricool\Features\Onboarding;

use Metricool\Features\Onboarding\Exceptions\BrandAccessDeniedException;
use Metricool\Features\Onboarding\Exceptions\TooManyBrandsException;
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

        return $routes;
    }

    /**
     * Handle incoming login request.
     *
     */
    public function login(\WP_REST_Request $request): \WP_REST_Response
    {
        // todo: storage ?
        $email = (string) $request->get_param('email');
        $password = (string) $request->get_param('password');

        // Validate fields
        if (!is_email($email) || empty($password)) {
            return $this->sendHttpErrorResponse(
                __('Validation failed.', 'metricool'),
                [],
                422
            );
        }

        // Attempt to login
        try {
            $this->auth->login($email, $password);
        } catch (\Exception $e) {
            return $this->sendHttpErrorResponse(
                __('The combination of username and password was incorrect.', 'metricool'),
                [],
                401
            );
        }

        // Retrieve the brands for completing the onboarding process
        $brands = $this->onboarding->mockUpBrands();

        // Attempt to automatically set the blog information
        try {
            $this->onboarding->findAndRetrieveBlogInfo($brands);
        } catch (TooManyBrandsException $e) {
            // Return the brands that were found when blogId could not be automatically set, so the user can select one
            return $this->sendHttpResponse([
                'onboarding_finished' => false,
                'message' => __('Please select a brand.', 'metricool'), // todo: better message
                'connected_brands' => $brands,
            ]);
        }

        // Check if the necessary authentication data is present
        if (!$this->api->hasAuthentication()) {
            // Todo: should we remove everything from database when this happens so user is not stuck?
            return $this->sendHttpErrorResponse(__('Something went wrong.'), [], 502);
        }

        // Finish onboarding
        $this->onboarding->setOnboardingCompleted();

        return $this->sendHttpResponse([
            'onboarding_finished' => true,
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
            $this->accounts->createAccount($captcha, $email, $password, $marketing);
            $brands = $this->onboarding->mockUpBrands();
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            // Catch RequestException because it could be an email exists error
            $response = $e->getResponse();

            // Metricool return a 400 response with the same body on validation errors and existing e-mail addresses
            // Because we already did the validation, we can assume that it's an e-mail exists error
            $message = $response->getStatusCode() == 400
                ? __('E-mail already exists', 'metricool')
                : __('Unknown Error. Please try again later.', 'metricool');

            return $this->sendHttpErrorResponse(
                $message,
                ['error' => (string) $response->getBody()->getContents()],
                $response->getStatusCode()
            );
        }

        // Attempt to automatically set the blog information
        try {
            $this->onboarding->findAndRetrieveBlogInfo($brands);
        } catch (TooManyBrandsException $e) {
            // Return the brands that were found when blogId could not be automatically set, so the user can select one
            return $this->sendHttpResponse([
                'onboarding_finished' => false,
                'message' => __('Please select a brand.', 'metricool'), // todo: better message
                'connected_brands' => $brands,
            ]);
        }

        if (!$this->api->hasAuthentication()) {
            // Return the brands that were found when blogId could not be automatically set, so the user can select one
            return $this->sendHttpResponse([
                'message' => __('Something went wrong.', 'metricool'),
                'finish_onboarding' => false,
            ]);
        }

        return $this->sendHttpResponse([
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
            } catch (BrandAccessDeniedException $e) {
                $brands = $this->onboarding->mockUpBrands();

                return $this->sendHttpResponse([
                    'onboarding_finished' => false,
                    'message' => __('Brand Access denied.', 'metricool'), // todo: better message
                    'connected_brands' => $brands,
                ]);
            }
        }

        // Check if the necessary authentication data is present
        if (!$this->api->hasAuthentication()) {
            return $this->sendHttpErrorResponse('Onboarding failed. User is not authenticated.');
        }

        $code = 200;
        $message = __('Successfully finished onboarding!', 'metricool');

        $success = $this->onboarding->setOnboardingCompleted();
        if (!$success) {
            $message = __('An error occurred while finishing the onboarding process', 'metricool');
            $code = 500;
        }

        return $this->sendHttpResponse([], $success, $message, $code);
    }
}
