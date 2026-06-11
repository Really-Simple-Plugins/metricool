<?php

namespace Metricool\Features\Onboarding\Services;

use GuzzleHttp\Exception\GuzzleException;
use Metricool\Http\Metricool\MetricoolApi;
use Metricool\Support\Helpers\Storages\EnvironmentConfig;

class OAuthService
{
    private EnvironmentConfig $env;
    private MetricoolApi $api;

    public function __construct(EnvironmentConfig $env, MetricoolApi $api)
    {
        $this->env = $env;
        $this->api = $api;
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

    /**
     * Authenticates the user using the OAuth code and state parameters.
     */
    public function authenticateWithCode(string $code, string $state): bool
    {
        // Verify state to prevent CSRF
        $validated = $this->validateState($state);

        if ($validated === false) {
            throw new \RuntimeException('invalid_state');
        }

        try {
            // Exchange the code for auth tokens
            $tokenData = $this->api->exchangeOAuthCode($code, $this->getRedirectUrl());
        } catch (GuzzleException $e) {
            throw new \RuntimeException('token_exchange_failed');
        }

        if (empty($tokenData['access_token']) || empty($tokenData['refresh_token']) || empty($tokenData['expires_in'])) {
            throw new \RuntimeException('missing_token_data');
        }

        // Retrieve the user ID from the access token
        $userId = $this->parseUserIdFromAccessToken($tokenData['access_token']);

        if (empty($userId)) {
            throw new \RuntimeException('token_parse_failed');
        }

        // Authenticate the Metricool API Client
        $this->api->authenticate(
            $userId,
            (string) $tokenData['access_token'],
            (string) $tokenData['refresh_token'],
            (int) ($tokenData['expires_in'])
        );

        return true;
    }

    /**
     * Validates the state parameter from the OAuth flow.
     */
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

    /**
     * Retrieves the stored state parameter from the transient. Returns an empty string if not found.
     */
    private function getStoredState(): string
    {
        return (string) get_option('metricool_oauth_state');
    }

    /**
     * Stores the state parameter in a transient for later validation. The transient expires after 15 minutes.
     */
    private function storeState(string $state): void
    {
        update_option('metricool_oauth_state', $state, false);
    }

    /**
     * Deletes the stored state parameter from the transient.
     */
    private function deleteStoredState(): void
    {
        delete_option('metricool_oauth_state');
    }

    /**
     * Parses the user ID from the given access token. The access token is expected to be a JWT with a compressed payload.
     */
    public function parseUserIdFromAccessToken(string $accessToken): ?string
    {
        $parts = explode('.', $accessToken);
        if (count($parts) !== 3) {
            return null;
        }

        // Step 1 – base64url-decode the payload (second segment)
        $payloadB64 = $parts[1];
        $payloadBytes = base64_decode(strtr($payloadB64, '-_', '+/'));
        if (!$payloadBytes) {
            return null;
        }

        // Step 2 – if compressed, decompress (zlib DEFLATE with header, wbits = 15)
        $decoded = zlib_decode($payloadBytes);
        $json = ($decoded !== false ? $decoded : $payloadBytes);
        if (empty($json)) {
            return null;
        }

        // Step 3 – decode JSON and read the "sub" claim
        $claims = json_decode($json, true);
        if (!is_array($claims) || empty($claims['sub'])) {
            return null;
        }

        // sub is "user:999999" – extract the numeric part
        $subject = $claims['sub'];
        if (strpos($subject, 'user:') === 0) {
            return substr($subject, strlen('user:'));
        }

        return null;
    }
}
