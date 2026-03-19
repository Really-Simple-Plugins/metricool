<?php

namespace Metricool\Features\Onboarding\Services;

use Metricool\Support\Helpers\Storages\EnvironmentConfig;

class OAuthService
{
    private EnvironmentConfig $env;

    public function __construct(EnvironmentConfig $env)
    {
        $this->env = $env;
    }
    /**
     * Retrieves the redirect URL for the OAuth flow.
     */
    public function getRedirectUrl(): string
    {
        return rest_url($this->env->getString('http.namespace') . '/' . $this->env->getString('http.version') . '/onboarding/oauth_callback');
    }

    /**
     * Generates the authorization URL for the OAuth flow, including a state parameter for security.
     */
    public function getAuthorizationUrl(): string
    {
        $state = $this->generateState();
        $redirectUri = $this->getRedirectUrl();

        return add_query_arg([
            'client_id' => $this->env->getString('metricool.oauth_client_id'),
            'state' => $state,
            'response_type' => 'code',
            'redirect_uri' => $redirectUri,
            'code_challenge' => 'login',
        ], $this->env->getString('metricool.oauth_authorize_url'));
    }

    public function validateState(string $state): bool
    {
        $storedState = get_transient('metricool_oauth_state');
        delete_transient('metricool_oauth_state');

        return $state === $storedState;
    }

    /**
     * Generates a unique state parameter for the OAuth flow and stores it in a transient.
     * The state parameter is used to prevent CSRF attacks.
     */
    private function generateState(): string
    {
        $state = wp_generate_password(32, false);
        set_transient('metricool_oauth_state', $state, 10 * MINUTE_IN_SECONDS);

        return $state;
    }
}
