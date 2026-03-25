<?php

declare(strict_types=1);

namespace Metricool\Features\Onboarding;

use GuzzleHttp\Exception\GuzzleException;
use Metricool\Features\Onboarding\Exceptions\BrandAccessDeniedException;
use Metricool\Features\Onboarding\Exceptions\CreateAccountException;
use Metricool\Features\Onboarding\Services\CreateAccountService;
use Metricool\Features\Onboarding\Services\OAuthService;
use Metricool\Http\Metricool\MetricoolApi;
use Metricool\Interfaces\FeatureInterface;
use Metricool\Services\DashboardService;
use Metricool\Support\Helpers\Storages\EnvironmentConfig;
use Metricool\Support\Helpers\Storages\RequestStorage;
use Metricool\Traits\HasRestAccess;

class OnboardingController implements FeatureInterface
{
    use HasRestAccess;

    private MetricoolApi $api;
    private OnboardingService $onboarding;
    private EnvironmentConfig $env;
    private CreateAccountService $accounts;
    private OAuthService $oauth;
    private RequestStorage $request;
    private DashboardService $dashboard;

    public function __construct(
        MetricoolApi $api,
        OnboardingService $onboarding,
        CreateAccountService $accounts,
        EnvironmentConfig $env,
        OAuthService $oauth,
        RequestStorage $request,
        DashboardService $dashboard
    ) {
        $this->api = $api;
        $this->onboarding = $onboarding;
        $this->accounts = $accounts;
        $this->env = $env;
        $this->oauth = $oauth;
        $this->request = $request;
        $this->dashboard = $dashboard;
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
        $email = (string) $request->get_param('email');
        $password = (string) $request->get_param('password');
        $marketing = (bool) $request->get_param('marketing');
        $terms = (bool) $request->get_param('terms');
        $captcha = (string) $request->get_param('captcha');

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
            return $this->sendHttpErrorResponse($e->getMessage(), ['reason' => $e->reason], 422);
        }

        return $this->sendHttpResponse([
            'onboarding' => [
                'state' => $this->dashboard->state(),
                'mode' => $this->dashboard->mode(),
            ],
        ]);
    }

    /**
     * Method is used to finish the onboarding process. It is called when the
     * user has completed the onboarding process and wants to finish it.
     */
    public function finishOnboarding(\WP_REST_Request $request): \WP_REST_Response
    {
        $blogId = (string) $request->get_param('blogId');

        // Store the blogId if it was provided by the client, to store the necessary blog information
        if (!empty($blogId)) {
            try {
                $this->onboarding->finalizeOnboarding($blogId);
            } catch (BrandAccessDeniedException $e) {
                return $this->sendHttpErrorResponse(__('Brand Access denied, please select a different brand.', 'metricool'), [], 403);
            } catch (GuzzleException $e) {
                return $this->sendHttpErrorResponse(__('Something went wrong.', 'metricool'));
            }
        }

        return $this->sendHttpResponse([
            'onboarding' => [
                'state' => $this->dashboard->state(),
                'mode' => $this->dashboard->mode(),
            ],
        ]);
    }

    /**
     * Build and return the Metricool OAuth authorize URL.
     */
    public function oauthRedirect(\WP_REST_Request $request): \WP_REST_Response
    {
        // Check if authorizationUrl is a secure URL, otherwise redirect to the dashboard
        if (is_ssl() === false) {
            return $this->sendHttpErrorResponse(__('HTTPS is required to be able to authorize this website.', 'metricool'), [], 400);
        }

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

        try {
            $this->oauth->authenticateWithCode($code, $state);
        } catch (\Exception $e) {
            wp_safe_redirect(add_query_arg('oauth_error', $e->getMessage(), $this->env->getString('plugin.dashboard_url')));
            exit;
        }

        // Attempt to automatically set the blog information, completes the onboarding process on success
        try {
            $this->onboarding->finalizeOnboarding();
        } catch (BrandAccessDeniedException | GuzzleException $e) {
            wp_safe_redirect(add_query_arg('oauth_error', 'onboarding_failed', $this->env->getString('plugin.dashboard_url')));
            exit;
        }

        // Redirect to the WordPress dashboard
        wp_safe_redirect($this->env->getString('plugin.dashboard_url'));
        exit;
    }

    /**
     * Permission check for the OAuth callback endpoint. The callback comes from
     * an external redirect, so it won't have nonce's. We verify the user is a
     * logged-in WP admin instead.
     */
    public function oauthCallbackPermissionCheck(\WP_REST_Request $request): bool
    {
        return true;
    }
}
