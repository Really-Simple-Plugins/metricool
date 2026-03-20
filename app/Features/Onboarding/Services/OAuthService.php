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
        $storedState = $this->getStoredState();
        $this->deleteStoredState();

        return $state === $storedState;
    }

    /**
     * Generates a unique state parameter for the OAuth flow and stores it in a transient.
     * The state parameter is used to prevent CSRF attacks.
     */
    private function generateState(): string
    {
        $state = wp_generate_password(32, false);

        $this->storeState($state);

        return $state;
    }

    private function getStoredState(): string
    {
        return get_option('metricool_oauth_state');
    }

    private function storeState(string $state): void
    {
        update_option('metricool_oauth_state', $state);
    }

    private function deleteStoredState(): void
    {
        delete_option('metricool_oauth_state');
    }

    public function parseUserIdFromAccessToken(string $accessToken): ?string
    {
        $parts = explode('.', $accessToken);
        if (count($parts) !== 3) {
            return null;
        }

        // Step 1 – base64url-decode the payload (second segment)
        $payloadB64 = $parts[1];
        $payloadBytes = base64_decode(strtr($payloadB64, '-_', '+/'));
        if ($payloadBytes === false) {
            return null;
        }

        // Step 2 – decompress (zlib DEFLATE with header, wbits = 15)
        $json = zlib_decode($payloadBytes);
        if ($json === false) {
            return null;
        }

        // Step 3 – decode JSON and read the "sub" claim
        $claims = json_decode($json, true);
        if (!is_array($claims) || empty($claims['sub'])) {
            return null;
        }

        // sub is "user:999999" – extract the numeric part
        $subject = $claims['sub'];
        if (str_starts_with($subject, 'user:')) {
            return substr($subject, strlen('user:'));
        }

        return $subject;
    }
}
