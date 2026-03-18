<?php

declare(strict_types=1);

namespace Metricool\Features\Onboarding;

use GuzzleHttp\Exception\GuzzleException;
use Metricool\Features\Onboarding\Exceptions\BrandAccessDeniedException;
use Metricool\Features\Onboarding\Exceptions\TooManyBrandsException;
use Metricool\Features\Onboarding\Services\AuthService;
use Metricool\Features\Onboarding\Services\CreateAccountService;
use Metricool\Http\Metricool\MetricoolApi;
use Metricool\Interfaces\FeatureInterface;
use Metricool\Services\TrackingScriptService;
use Metricool\Support\Helpers\Storages\EnvironmentConfig;
use Metricool\Traits\HasRestAccess;

class OnboardingController implements FeatureInterface
{
    use HasRestAccess;

    private MetricoolApi $api;
    private OnboardingService $onboarding;
    private CreateAccountService $accounts;
    private AuthService $auth;
    private EnvironmentConfig $env;

    public function __construct(MetricoolApi $api, OnboardingService $onboarding, CreateAccountService $accounts, AuthService $auth, TrackingScriptService $tracking, EnvironmentConfig $env)
    {
        $this->api = $api;
        $this->onboarding = $onboarding;
        $this->accounts = $accounts;
        $this->auth = $auth;
        $this->env = $env;
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

        $routes['onboarding/oauth_redirect'] = [
            'methods' => 'GET',
            'callback' => [$this, 'oauthRedirect'],
        ];

        $routes['onboarding/oauth_callback'] = [
            'methods' => 'GET',
            'callback' => [$this, 'oauthCallback'],
            'permission_callback' => [$this, 'oauthCallbackPermissionCheck'],
        ];

        return $routes;
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

    /**
     * Build and return the Metricool OAuth authorize URL.
     */
    public function oauthRedirect(\WP_REST_Request $request): \WP_REST_Response
    {
        $state = wp_generate_password(32, false);
        set_transient('metricool_oauth_state', $state, 10 * MINUTE_IN_SECONDS);

        $redirectUri = rest_url($this->env->getString('http.namespace') . '/' . $this->env->getString('http.version') . '/onboarding/oauth_callback');

        $authorizeUrl = add_query_arg([
            'client_id' => $this->env->getString('metricool.oauth_client_id'),
            'state' => $state,
            'response_type' => 'code',
            'redirect_uri' => $redirectUri,
            'code_challenge' => 'login',
        ], $this->env->getString('metricool.oauth_authorize_url'));

        return $this->sendHttpResponse([
            'redirect_url' => $authorizeUrl,
        ]);
    }

    /**
     * Handle the OAuth callback from Metricool. Exchanges the authorization
     * code for tokens, authenticates the user, and redirects to the dashboard.
     */
    public function oauthCallback(\WP_REST_Request $request): \WP_REST_Response
    {
        $code = (string) $request->get_param('code');
        $state = (string) $request->get_param('state');

        // Verify state to prevent CSRF
        $storedState = get_transient('metricool_oauth_state');
        delete_transient('metricool_oauth_state');

        if (empty($state) || $state !== $storedState) {
            wp_safe_redirect(add_query_arg('metricool_error', 'invalid_state', $this->env->getString('plugin.dashboard_url')));
            exit;
        }

        if (empty($code)) {
            wp_safe_redirect(add_query_arg('metricool_error', 'missing_code', $this->env->getString('plugin.dashboard_url')));
            exit;
        }

        // Build the redirect_uri (must match what was sent in the authorize request)
        $redirectUri = rest_url($this->env->getString('http.namespace') . '/' . $this->env->getString('http.version') . '/onboarding/oauth_callback');

        try {
            // Exchange the code for tokens
            $tokenData = $this->api->exchangeOAuthCode($code, $redirectUri);

            // Authenticate - store userId, accessToken, refreshToken
            $this->api->authenticate(
                (string) ($tokenData['user_id'] ?? $tokenData['userId'] ?? ''),
                (string) ($tokenData['access_token'] ?? $tokenData['accessToken'] ?? ''),
                (string) ($tokenData['refresh_token'] ?? $tokenData['refreshToken'] ?? ''),
                (int) ($tokenData['expires_in'] ?? $tokenData['expires_in'] ?? 300)
            );

            // Retrieve brands and attempt to auto-select
            $brands = $this->api->brands()->all();

            try {
                $this->onboarding->findAndRetrieveBlogInfo($brands);
                $this->onboarding->setOnboardingCompleted();
            } catch (TooManyBrandsException $e) {
                // User needs to select a brand - redirect to dashboard, frontend will handle brand selection
            }
        } catch (\Exception $e) {
            wp_safe_redirect(add_query_arg('metricool_error', 'token_exchange_failed', $this->env->getString('plugin.dashboard_url')));
            exit;
        }

        // Redirect to the WordPress dashboard
        wp_safe_redirect($this->env->getString('plugin.dashboard_url'));
        exit;
    }

    /**
     * Permission check for the OAuth callback endpoint. The callback comes from
     * an external redirect, so it won't have a nonce. We verify the user is a
     * logged-in WP admin instead.
     */
    public function oauthCallbackPermissionCheck(\WP_REST_Request $request): bool
    {
        return current_user_can('manage_options');
    }
}
