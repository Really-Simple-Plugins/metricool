<?php

declare(strict_types=1);

namespace Metricool\Http\Metricool;

use Carbon\Carbon;
use RuntimeException;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use InvalidArgumentException;
use Metricool\Services\OptionsService;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\GuzzleException;
use Metricool\Http\Metricool\Exceptions\ApiException;
use Metricool\Support\Helpers\Storages\EnvironmentConfig;
use Throwable;

class MetricoolClient
{
    private const OPTION_USER_ID = 'metricool_user_id';
    private const OPTION_BLOG_ID = 'metricool_blog_id';
    private const OPTION_AUTH_TOKEN = 'metricool_auth_token';
    private const OPTION_REFRESH_TOKEN = 'metricool_refresh_token';
    private const OPTION_AUTH_TOKEN_EXPIRES = 'metricool_auth_token_expires';
    private const OPTION_REFRESH_LOCK = 'metricool_refresh_lock';

    private const LOCK_STALE_MS = 15000;
    private const LOCK_WAIT_SLEEP_MS = 100;
    private const LOCK_WAIT_MAX_MS = 5000;

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
        update_option(self::OPTION_USER_ID, $userId);

        $this->setUserId($userId);
    }

    /**
     * Clear the persisted Metricool user ID.
     */
    public function clearUserId(): void
    {
        delete_option(self::OPTION_USER_ID);

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
        update_option(self::OPTION_BLOG_ID, $blogId);

        $this->setBlogId($blogId);
    }

    /**
     * Clear the persisted Metricool blog ID.
     */
    public function clearBlogId(): void
    {
        delete_option(self::OPTION_BLOG_ID);

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
        update_option(self::OPTION_AUTH_TOKEN, $token);

        $this->setUserToken($token);
    }

    /**
     * Clear the persisted access token.
     */
    public function clearUserToken(): void
    {
        delete_option(self::OPTION_AUTH_TOKEN);

        $this->setUserToken('');
    }

    /**
     * Get the persisted refresh token.
     */
    public function getRefreshToken(): string
    {
        return get_option(self::OPTION_REFRESH_TOKEN);
    }

    /**
     * Persist the refresh token.
     */
    public function storeRefreshToken(string $refreshToken): void
    {
        update_option(self::OPTION_REFRESH_TOKEN, $refreshToken);
    }

    /**
     * Clear the persisted refresh token data.
     */
    public function clearRefreshToken(): void
    {
        delete_option(self::OPTION_REFRESH_TOKEN);
        delete_option(self::OPTION_AUTH_TOKEN_EXPIRES);
    }

    /**
     * Get the token expiration timestamp with a raw query to avoid retrieving
     * the option from the WordPress object cache.
     * @internal Not using get_option() is on purpose!
     */
    public function getTokenExpires(): int
    {
        global $wpdb;

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
        return (int) $wpdb->get_var(
            $wpdb->prepare(
                "SELECT option_value FROM {$wpdb->options} WHERE option_name = %s",
                self::OPTION_AUTH_TOKEN_EXPIRES
            )
        );
    }

    /**
     * Get the token expiration as a Carbon date.
     */
    public function tokenExpiresAt(): Carbon
    {
        return Carbon::createFromTimestamp($this->getTokenExpires());
    }

    /**
     * Determine whether the access token is expired. This uses a 1-minute buffer,
     * to account for clock skew and request time.
     */
    public function isTokenExpired(): bool
    {
        return Carbon::now()->gt($this->tokenExpiresAt()->subMinute());
    }

    /**
     * Persist the token expiration time.
     */
    public function storeTokenExpires(int $expiresIn): void
    {
        $expiresIn = Carbon::now()->addSeconds($expiresIn)->timestamp;

        update_option(self::OPTION_AUTH_TOKEN_EXPIRES, $expiresIn);
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
     * @throws ApiException
     */
    public function get(string $endpoint): ?array
    {
        return $this->request('GET', $endpoint);
    }

    /**
     * Send a POST request.
     * @throws ApiException
     */
    public function post(string $endpoint, array $body): ?array
    {
        return $this->request('POST', $endpoint, $body);
    }

    /**
     * Send a PUT request.
     * @throws ApiException
     */
    public function put(string $endpoint, array $body): ?array
    {
        return $this->request('PUT', $endpoint, $body);
    }

    /**
     * Send a PATCH request.
     * @throws ApiException
     */
    public function patch(string $endpoint, array $body): ?array
    {
        return $this->request('PATCH', $endpoint, $body);
    }

    /**
     * Send a DELETE request.
     * @throws ApiException
     */
    public function delete(string $endpoint): ?array
    {
        return $this->request('DELETE', $endpoint);
    }

    /**
     * Send an authenticated request to the Metricool API.
     *
     * @param mixed|null $body
     * @throws ApiException
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
        } catch (Throwable $e) {
            if ($e instanceof GuzzleException && $e->getCode() === 401) {
                $this->logout();
            }

            throw new ApiException(
                $e->getMessage(),
                $e->getCode(),
                $e
            );
        }

        return $this->parseResponse($response);
    }

    /**
     * Exchange an OAuth authorization code for an access token.
     * @throws ApiException
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

        try {
            $response = $this->client->send(
                new Request('POST', $this->env->getString('metricool.oauth_token_url'), $headers),
                $options
            );
        } catch (Throwable $e) {
            throw new ApiException(
                $e->getMessage(),
                $e->getCode(),
                $e
            );
        }

        return $this->parseResponse($response);
    }

    /**
     * Refresh the authentication token using the refresh token.
     *
     * Uses a MySQL lock to prevent concurrent processes from both
     * attempting a refresh. The process that cannot acquire the lock
     * waits in a loop until the token is refreshed by the lock holder.
     *
     * @throws ApiException when the refresh request fails or the
     *                       response is invalid.
     * @throws RuntimeException when polling times out.
     */
    public function refreshAuthToken(): void
    {
        $lockAcquired = $this->lockTokenRefresh();
        if ($lockAcquired === false) {
            $this->pollForNewUserToken();
            return;
        }

        try {
            $this->performTokenRefresh();
        } finally {
            $this->releaseRefreshLock();
        }
    }

    /**
     * Acquire a lock via wp_options to serialize token refresh attempts.
     * Uses INSERT IGNORE for atomicity: only one process can create the row.
     */
    private function lockTokenRefresh(): bool
    {
        global $wpdb;

        // Remove stale locks that might be left behind if a process crashes during refresh. We consider locks older than LOCK_STALE_MS as stale.
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
        $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM {$wpdb->options} WHERE option_name = %s AND option_value < %d",
                self::OPTION_REFRESH_LOCK,
                time() - (int) (self::LOCK_STALE_MS / 1000)
            )
        );

        // Attempt to insert the lock row. INSERT IGNORE ensures only one process succeeds when racing concurrently.
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
        $result = $wpdb->query(
            $wpdb->prepare(
                "INSERT IGNORE INTO {$wpdb->options} (option_name, option_value, autoload) VALUES (%s, %d, 'no')",
                self::OPTION_REFRESH_LOCK,
                time()
            )
        );

        return ($result !== false && $result > 0);
    }

    /**
     * Wait for another process to refresh the token by polling if the token is expired
     * @throws RuntimeException if the token is still expired after waiting for the maximum time.
     */
    private function pollForNewUserToken(): void
    {
        $maxWait = self::LOCK_WAIT_MAX_MS;
        $sleepDuration = self::LOCK_WAIT_SLEEP_MS;
        $waited = 0;

        while ($waited < $maxWait) {
            if ($this->isTokenExpired() === false) {
                $this->setUserToken($this->fetchUserToken());
                return;
            }

            usleep($sleepDuration * 1000);
            $waited += $sleepDuration;
        }

        throw new \RuntimeException('Timed out waiting for token refresh. Please try again.');
    }

    /**
     * Read the access token directly from the database, bypassing the
     * WordPress object cache.
     */
    private function fetchUserToken(): string
    {
        global $wpdb;

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
        return (string) $wpdb->get_var(
            $wpdb->prepare(
                "SELECT option_value FROM {$wpdb->options} WHERE option_name = %s",
                self::OPTION_AUTH_TOKEN
            )
        );
    }

    /**
     * Perform the actual token refresh request against the
     * Metricool OAuth endpoint.
     *
     * @throws ApiException when the refresh request fails or the
     *                       response is invalid.
     */
    private function performTokenRefresh(): void
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
        } catch (Throwable $e) {
            $this->logout();

            throw new ApiException(
                'Failed to refresh authentication token. Please log in again.',
                $e->getCode(),
                $e
            );
        }

        $data = $this->parseResponse($response);

        if (!isset($data['access_token'], $data['refresh_token'], $data['expires_in'])) {
            throw new ApiException('refresh_token response invalid.');
        }

        $this->storeUserToken($data['access_token']);
        $this->storeRefreshToken($data['refresh_token']);
        $this->storeTokenExpires($data['expires_in']);
    }

    /**
     * Release the wp_options lock after a token refresh.
     */
    private function releaseRefreshLock(): void
    {
        delete_option(self::OPTION_REFRESH_LOCK);
    }

    /**
     * Decode a JSON response body into an array.
     *
     * @throws ApiException when the response body is empty or
     *                      not valid JSON.
     */
    private function parseResponse(ResponseInterface $response): array
    {
        $response->getBody()->rewind();
        $decoded = json_decode($response->getBody()->getContents(), true);

        if (!is_array($decoded)) {
            throw new ApiException('Invalid JSON response from the API.');
        }

        return $decoded;
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
                esc_html(implode(', ', $validationErrors))
            );
        }
    }
}
