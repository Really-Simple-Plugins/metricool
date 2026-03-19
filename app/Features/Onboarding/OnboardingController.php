<?php

declare(strict_types=1);

namespace Metricool\Features\Onboarding;

use _PHPStan_e870ac104\Nette\Neon\Exception;
use GuzzleHttp\Exception\GuzzleException;
use Metricool\Features\Onboarding\Exceptions\BrandAccessDeniedException;
use Metricool\Features\Onboarding\Exceptions\CreateAccountException;
use Metricool\Features\Onboarding\Services\CreateAccountService;
use Metricool\Features\Onboarding\Services\OAuthService;
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
    private EnvironmentConfig $env;
    private CreateAccountService $accounts;
    private OAuthService $oauth;

    public function __construct(
        MetricoolApi $api,
        OnboardingService $onboarding,
        CreateAccountService $accounts,
        TrackingScriptService $tracking,
        EnvironmentConfig $env,
        OAuthService $oauth
    ) {
        $this->api = $api;
        $this->onboarding = $onboarding;
        $this->accounts = $accounts;
        $this->env = $env;
        $this->oauth = $oauth;
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
        } catch (CreateAccountException $e) {
            return $this->sendHttpErrorResponse($e->getMessage(), ['reason' => $e->reason], $e->getCode());
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

        return $this->sendHttpResponse([
            'onboarding' => $this->onboarding->state()
        ]);
    }

    /**
     * Build and return the Metricool OAuth authorize URL.
     */
    public function oauthRedirect(\WP_REST_Request $request): \WP_REST_Response
    {
        return $this->sendHttpResponse([
            'redirect_url' => $this->oauth->getAuthorizationUrl(),
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
        $redirectUri = $this->oauth->getRedirectUrl();

        try {
            // Exchange the code for tokens
            $tokenData = $this->api->exchangeOAuthCode($code, $redirectUri);

            if (empty($tokenData['user_id']) || empty($tokenData['access_token']) || empty($tokenData['refresh_token'])) {
                throw new Exception('Token data is missing');
            }

            // Authenticate - store userId, accessToken, refreshToken
            $this->api->authenticate(
                (string) $tokenData['user_id'],
                (string) $tokenData['access_token'],
                (string) $tokenData['refresh_token'],
                (int) ($tokenData['expires_in'])
            );
        } catch (\Exception $e) {
            wp_safe_redirect(add_query_arg('metricool_error', 'token_exchange_failed', $this->env->getString('plugin.dashboard_url')));
            exit;
        }

        // Retrieve brands and attempt to auto-select
        $this->onboarding->findAndRetrieveBlogInfo();

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
