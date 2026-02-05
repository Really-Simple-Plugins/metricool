<?php

declare(strict_types=1);

namespace Metricool\Http\Metricool;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use Metricool\Support\Helpers\Storages\EnvironmentConfig;
use Psr\Http\Message\ResponseInterface;

/**
 * @todo Add error handling either with try-catches here, in the resources or
 * @todo on implementation level.
 */
class MetricoolClient
{
    private ?Client $client = null;
    private string $apiUrl;
    private string $userToken = '';
    private string $refreshToken = '';
    private string $blogId = '';
    private string $userId = '';
    protected array $middleWares = [];

    public function __construct(EnvironmentConfig $env)
    {
        $this->apiUrl = $env->get('metricool.base_api_domain');
    }

    public function setUserId(string $userId): void
    {
        $this->userId = $userId;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function hasUserId(): bool
    {
        return !empty($this->userId);
    }

    public function storeUserId(string $userId): void
    {
        update_option('metricool_user_id', $userId);

        $this->setUserId($userId);
    }

    public function getBlogId(): string
    {
        return $this->blogId;
    }

    public function setBlogId(string $blogId): void
    {
        $this->blogId = $blogId;
    }

    public function hasBlogId(): bool
    {
        return !empty($this->blogId);
    }

    public function getUserToken(): string
    {
        return $this->userToken;
    }

    public function setUserToken(string $userToken): void
    {
        $this->userToken = $userToken;
    }

    public function hasUserToken(): bool
    {
        return !empty($this->userToken);
    }

    public function storeUserToken(string $token): void
    {
        update_option('metricool_auth_token', $token);

        $this->setUserToken($token);
    }

    public function getRefreshToken(): string
    {
        return $this->refreshToken;
    }

    public function setRefreshToken(string $refreshToken): void
    {
        $this->refreshToken = $refreshToken;
    }

    public function hasRefreshToken(): bool
    {
        return !empty($this->refreshToken);
    }

    public function storeRefreshToken(string $refreshToken): void
    {
        update_option('metricool_refresh_token', $refreshToken);

        $this->setRefreshToken($refreshToken);
    }

    public function insertMiddleWare(callable $middleWare): void
    {
        $this->middleWares[] = $middleWare;
    }

    public function connect(): Client
    {
        return $this->client();
    }

    public function isConnected(): bool
    {
        return ($this->client instanceof Client);
    }

    /**
     * Set the authentication tokens and userId.
     */
    public function authenticate(string $userId, string $userToken, string $refreshToken): self
    {
        $this->storeUserId($userId);
        $this->storeUserToken($userToken);
        $this->storeRefreshToken($refreshToken);

        return $this;
    }

    public function hasAuthentication(): bool
    {
        return $this->hasUserToken() && $this->hasUserId() && $this->hasBlogId();
    }

    private function client(): CLient
    {
        if ($this->client) {
            return $this->client;
        }

        $handlerStack = HandlerStack::create();
        foreach ($this->middleWares as $middleWare) {
            $handlerStack->push($middleWare);
        }

        $this->client = new Client([
            'http_errors' => true,
            'handler' => $handlerStack,
            'expect' => false,
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ]
        ]);

        return $this->client;
    }

    /**
     * @throws GuzzleException
     */
    public function get(string $endpoint): ?array
    {
        return $this->request('GET', $endpoint);
    }

    /**
     * @throws GuzzleException
     */
    public function post(string $endpoint, array $body): ?array
    {
        return $this->request('POST', $endpoint, $body);
    }

    /**
     * @throws GuzzleException
     */
    public function put(string $endpoint, array $body): ?array
    {
        return $this->request('PUT', $endpoint, $body);
    }

    /**
     * @throws GuzzleException
     */
    public function patch(string $endpoint, string $body): ?array
    {
        return $this->request('PATCH', $endpoint, $body);
    }

    /**
     * @throws GuzzleException
     */
    public function delete(string $endpoint): ?array
    {
        return $this->request('DELETE', $endpoint);
    }

    /**
     * @param mixed|null $body
     * @throws GuzzleException
     */
    public function request(string $method, string $endpoint, $body = null): ?array
    {
        $this->validate();

        $response = $this->client->send(
            new Request($method, $this->formatUrl($endpoint), ['X-Mc-Auth' => $this->userToken], $body)
        );

        return $this->parseResponse($response);
    }

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

        if (empty($this->userToken)) {
            $validationErrors[] = 'User token is required to connect to Metricool API.';
        }

        if ($this->isConnected() === false) {
            $validationErrors[] = 'Client is not connected to Metricool API.';
        }

        if (!empty($validationErrors)) {
            throw new \InvalidArgumentException(
                'Metricool Client is not setup correctly: ' . PHP_EOL .
                implode(', ', $validationErrors)
            );
        }
    }
}
