<?php

declare(strict_types=1);

namespace Metricool\Features\Onboarding;

if (!defined('ABSPATH')) {
    exit;
}

use Metricool\Support\Helpers\Storages\RequestStorage;
use Metricool\Interfaces\FeatureInterface;
use Metricool\Features\Onboarding\Services\OAuthService;
use Metricool\Support\Helpers\Storages\EnvironmentConfig;
use Throwable;

class OnboardingController implements FeatureInterface
{
    private OnboardingService $onboarding;
    private EnvironmentConfig $env;
    private OAuthService $oauth;
    private RequestStorage $request;

    public function __construct(
        OnboardingService $onboarding,
        EnvironmentConfig $env,
        OAuthService $oauth,
        RequestStorage $request
    ) {
        $this->onboarding = $onboarding;
        $this->env = $env;
        $this->oauth = $oauth;
        $this->request = $request;
    }

    public function register(): void
    {
        add_action('load-toplevel_page_metricool', [$this, 'oauthCallback']);
    }

    /**
     * Handle the OAuth callback from Metricool. Exchanges the authorization
     * code for tokens, authenticates the user, and redirects to the dashboard.
     */
    public function oauthCallback(): void
    {
        $action = $this->request->getString('global.metricool_action');

        if ($action !== OAuthService::REDIRECT_ACTION) {
            return;
        }

        $code = $this->request->getString('global.code');
        $state = $this->request->getString('global.state');

        try {
            $this->oauth->authenticateWithCode($code, $state);
        } catch (Throwable $e) {
            wp_safe_redirect(add_query_arg('oauth_error', $e->getMessage(), $this->env->getString('plugin.dashboard_url')));
            exit;
        }

        // Attempt to automatically set the blog information, completes the onboarding process on success
        try {
            $this->onboarding->finalizeOnboarding();
        } catch (Throwable $e) {
            wp_safe_redirect(add_query_arg('oauth_error', 'onboarding_failed', $this->env->getString('plugin.dashboard_url')));
            exit;
        }

        // Redirect to the WordPress dashboard
        wp_safe_redirect($this->env->getString('plugin.dashboard_url'));
        exit;
    }
}
