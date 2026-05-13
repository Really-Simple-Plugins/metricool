<?php

declare(strict_types=1);

namespace Metricool\Http\Metricool;

use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use Metricool\Services\OptionsService;
use Metricool\Support\Helpers\Storages\EnvironmentConfig;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;
use InvalidArgumentException;

class MetricoolClient
{
    private ?Client $client = null;

    private EnvironmentConfig $env;
    private OptionsService $options;

    private string $apiUrl;
    private string $userToken = '';
    private string $blogId = '';
    private string $userId = '';
    protected array $middleWares = [];


    /**
     * Create a new Metricool API client wrapper.
     */
    public function __construct(EnvironmentConfig $env, OptionsService $options)
    {
        $this->env = $env;
        $this->options = $options;
        $this->apiUrl = $env->get('metricool.base_api_domain');
    }

    /**
     * Set the authenticated Metricool user ID.
     */
    public function setUserId(string $userId): void
    {
        $this->userId = $userId;
    }

    /**
     * Get the authenticated Metricool user ID.
     */
    public function getUserId(): string
    {
        return $this->userId;
    }

    /**
     * Check whether a Metricool user ID is available.
     */
    public function hasUserId(): bool
    {
        return !empty($this->userId);
    }

    /**
     * Persist and set the Metricool user ID.
     */
    public function storeUserId(string $userId): void
    {
        update_option('metricool_user_id', $userId);

        $this->setUserId($userId);
    }

    /**
     * Clear the persisted Metricool user ID.
     */
    public function clearUserId(): void
    {
        delete_option('metricool_user_id');

        $this->setUserId('');
    }

    /**
     * Get the selected Metricool blog ID.
     */
    public function getBlogId(): string
    {
        return $this->blogId;
    }

    /**
     * Set the selected Metricool blog ID.
     */
    public function setBlogId(string $blogId): void
    {
        $this->blogId = $blogId;
    }

    /**
     * Persist and set the Metricool blog ID.
     */
    public function storeBlogId(string $blogId): void
    {
        update_option('metricool_blog_id', $blogId);

        $this->setBlogId($blogId);
    }

    /**
     * Clear the persisted Metricool blog ID.
     */
    public function clearBlogId(): void
    {
        delete_option('metricool_blog_id');

        $this->setBlogId('');
    }

    /**
     * Check whether a Metricool blog ID is available.
     */
    public function hasBlogId(): bool
    {
        return !empty($this->blogId);
    }

    /**
     * Get the current access token.
     */
    public function getUserToken(): string
    {
        return $this->userToken;
    }

    /**
     * Set the current access token.
     */
    public function setUserToken(string $userToken): void
    {
        $this->userToken = $userToken;
    }

    /**
     * Check whether an access token is available.
     */
    public function hasUserToken(): bool
    {
        return !empty($this->userToken);
    }

    /**
     * Persist and set the current access token.
     */
    public function storeUserToken(string $token): void
    {
        update_option('metricool_auth_token', $token);

        $this->setUserToken($token);
    }

    /**
     * Clear the persisted access token.
     */
    public function clearUserToken(): void
    {
        delete_option('metricool_auth_token');

        $this->setUserToken('');
    }

    /**
     * Get the persisted refresh token.
     */
    public function getRefreshToken(): string
    {
        return get_option('metricool_refresh_token');
    }

    /**
     * Persist the refresh token.
     */
    public function storeRefreshToken(string $refreshToken): void
    {
        update_option('metricool_refresh_token', $refreshToken);
    }

    /**
     * Clear the persisted refresh token data.
     */
    public function clearRefreshToken(): void
    {
        delete_option('metricool_refresh_token');
        delete_option('metricool_auth_token_expires');
    }

    /**
     * Get the token expiration timestamp.
     */
    public function getTokenExpires(): int
    {
        return (int) get_option('metricool_auth_token_expires') ?: 0;
    }

    /**
     * Get the token expiration as a Carbon date.
     */
    public function tokenExpiresAt(): Carbon
    {
        return Carbon::createFromTimestamp($this->getTokenExpires());
    }

    /**
     * Determine whether the access token is expired.
     */
    public function isTokenExpired(): bool
    {
        return Carbon::now()
            ->gt($this->tokenExpiresAt()->subMinute()); // add 1-minute buffer so refreshing happens before the token expires
    }

    /**
     * Persist the token expiration time.
     */
    public function storeTokenExpires(int $expiresIn): void
    {
        $expiresIn = Carbon::now()->addSeconds($expiresIn)->timestamp;

        update_option('metricool_auth_token_expires', $expiresIn);
    }

    /**
     * Register a middleware for outgoing requests.
     */
    public function insertMiddleWare(callable $middleWare): void
    {
        $this->middleWares[] = $middleWare;
    }

    /**
     * Connect and return the configured HTTP client.
     */
    public function connect(): Client
    {
        return $this->client();
    }

    /**
     * Check whether the HTTP client has been initialized.
     */
    public function isConnected(): bool
    {
        return ($this->client instanceof Client);
    }

    /**
     * Set the authentication tokens and userId.
     */
    public function authenticate(string $userId, string $userToken, string $refreshToken, int $expires): self
    {
        $this->storeUserId($userId);
        $this->storeUserToken($userToken);
        $this->storeRefreshToken($refreshToken);
        $this->storeTokenExpires($expires);

        return $this;
    }

    /**
     * Clear the authentication tokens and userId.
     */
    public function logout(): void
    {
        $this->options->wipe();
    }

    /**
     * Check if the client has all the necessary authentication tokens to show the dashboard
     */
    public function hasAuthentication(): bool
    {
        return $this->hasUserToken() && $this->hasUserId();
    }

    /**
     * Build the middleware stack for the HTTP client.
     */
    protected function middleware(): HandlerStack
    {
        $handlerStack = HandlerStack::create();
        foreach ($this->middleWares as $middleWare) {
            $handlerStack->push($middleWare);
        }
        return $handlerStack;
    }

    /**
     * Build or return the configured HTTP client instance.
     */
    private function client(): Client
    {
        if ($this->client) {
            return $this->client;
        }

        $this->client = new Client([
            'http_errors' => true,
            'handler' => $this->middleware(),
            'expect' => false,
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'User-Agent' => $this->getRequestUserAgent(),
            ]
        ]);

        return $this->client;
    }

    /**
     * Get the user agent string for the request.
     */
    public function getRequestUserAgent(): string
    {
        return "MetricoolPlugin/" . $this->env->getString('plugin.version') . " (WordPress/" . get_bloginfo('version') . "; PHP/" . phpversion() . "; ref: " . $this->getReferrer() . "; +" . site_url() . ")";
    }

    /**
     * EXTENDIFY_PARTNER_ID will contain the required value if WordPress is
     * configured using Extendify. Otherwise, use default 'wp'.
     */
    public function getReferrer(): string
    {
        return (defined('EXTENDIFY_PARTNER_ID') ? constant('EXTENDIFY_PARTNER_ID') : 'wp');
    }

    /**
     * Send a GET request.
     * @throws GuzzleException
     */
    public function get(string $endpoint): ?array
    {
        return $this->request('GET', $endpoint);
    }

    /**
     * Send a POST request.
     * @throws GuzzleException
     */
    public function post(string $endpoint, array $body): ?array
    {
        return $this->request('POST', $endpoint, $body);
    }

    /**
     * Send a PUT request.
     * @throws GuzzleException
     */
    public function put(string $endpoint, array $body): ?array
    {
        return $this->request('PUT', $endpoint, $body);
    }

    /**
     * Send a PATCH request.
     * @throws GuzzleException
     */
    public function patch(string $endpoint, array $body): ?array
    {
        return $this->request('PATCH', $endpoint, $body);
    }

    /**
     * Send a DELETE request.
     * @throws GuzzleException
     */
    public function delete(string $endpoint): ?array
    {
        return $this->request('DELETE', $endpoint);
    }

    /**
     * Send an authenticated request to the Metricool API.
     *
     * @param mixed|null $body
     * @throws GuzzleException
     */
    public function request(string $method, string $endpoint, $body = null): ?array
    {
        $this->validate();

        if ($this->isTokenExpired()) {
            $this->refreshAuthToken();
        }

        try {
            $response = $this->client->send(
                new Request($method, $this->formatUrl($endpoint), [
                    'Authorization' => 'Bearer ' . $this->userToken
                ], json_encode($body))
            );
        } catch (GuzzleException $e) {
            if ($e->getCode() === 401) {
                $this->logout();
            }

            throw $e;
        }

        return $this->parseResponse($response);
    }

    /**
     * Exchange an OAuth authorization code for an access token.
     * @throws GuzzleException
     */
    public function exchangeOAuthCode(string $code, string $redirectUri): array
    {
        $headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/x-www-form-urlencoded',
        ];

        $options = [
            'form_params' => [
                'grant_type' => 'authorization_code',
                'client_id' => $this->env->getString('metricool.oauth_client_id'),
                'code' => $code,
                'redirect_uri' => $redirectUri,
                'code_verifier' => 'login',
            ],
        ];

        $response = $this->client->send(
            new Request('POST', $this->env->getString('metricool.oauth_token_url'), $headers),
            $options
        );

        return $this->parseResponse($response);
    }

    /**
     * Refresh the authentication token using the refresh token.
     * @throws RuntimeException the user will be unauthenticated if the refresh token request fails.
     */
    public function refreshAuthToken(): void
    {
        $headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/x-www-form-urlencoded',
        ];

        $options = [
            'form_params' => [
                'client_id' => $this->env->getString('metricool.oauth_client_id'),
                'grant_type' => 'refresh_token',
                'refresh_token' => $this->getRefreshToken(),
            ]
        ];

        try {
            $response = $this->client->send(
                new Request('POST', $this->env->getString('metricool.oauth_token_url'), $headers),
                $options
            );
        } catch (GuzzleException $e) {
            // If the refresh token request fails, we need to log the user out.
            $this->logout();
            throw new RuntimeException('Failed to refresh authentication token. Please log in again.', 500, $e);
        }

        $data = $this->parseResponse($response);

        if (!isset($data['access_token'], $data['refresh_token'], $data['expires_in'])) {
            throw new RuntimeException('refresh_token response invalid.');
        }

        $this->storeUserToken($data['access_token']);
        $this->storeRefreshToken($data['refresh_token']);
        $this->storeTokenExpires($data['expires_in']);
    }

    /**
     * Decode a JSON response body into an array.
     */
    private function parseResponse(ResponseInterface $response): ?array
    {
        $response->getBody()->rewind();
        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Add userId and blogId to the URL as part of the authentication. When the
     * userId and blogId are not set, they will not be added to the URL, which
     * can still result in a successful request if the userToken is set and
     * valid.
     */
    private function formatUrl(string $url): string
    {
        $query = http_build_query(array_filter([
            'userId' => $this->userId,
            'blogId' => $this->blogId,
        ]));

        // Dirty hack to allow for non-standard query params
        // Metricool API supports urls with the same parameter multiple times
        // Example /v2/settings/users/:id?fields=alternativeEmail&fields=sendToAlternativeEmail
        $url = (strpos($url, '?') === false)
            ? $url . '?' . $query
            : $url . '&' . $query;

        return trailingslashit($this->apiUrl) . $url;
    }

    /**
     * Validate if all prerequisites are met to use the client. We need at least
     * the user token to be set before we can make any requests.
     * @throws \InvalidArgumentException
     */
    public function validate(): void
    {
        $validationErrors = [];

        if ($this->hasAuthentication() === false) {
            $validationErrors[] = 'Authentication is required for Metricool API.';
        }

        if ($this->isConnected() === false) {
            $validationErrors[] = 'Client is not connected to Metricool API.';
        }

        if (!empty($validationErrors)) {
            throw new InvalidArgumentException(
                'Metricool Client is not setup correctly: ' . PHP_EOL .
                implode(', ', $validationErrors)
            );
        }
    }
}
