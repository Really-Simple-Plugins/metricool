<?php

namespace Metricool\Http\Metricool;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

/**
 * @todo Add error handling either with try-catches here, in the resources or
 * @todo on implementation level.
 */
class MetricoolClient
{
    private ?Client $client = null;
    private string $apiUrl = 'https://app.metricool.com/api/';
    private string $stagingApiUrl = 'https://app.metricool.com/api/'; // todo
    private bool $testing = false;
    private string $userToken;
    private string $blogId;
    private string $userId;
    protected array $middleWares = [];

    public function setUserId(string $userId): void
    {
        $this->userId = $userId;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function setBlogId(string $blogId): void
    {
        $this->blogId = $blogId;
    }

    public function setUserToken(string $userToken): void
    {
        $this->userToken = $userToken;
    }

    public function isTesting(): bool
    {
        return $this->testing;
    }

    public function setTesting(bool $testing): void
    {
        $this->testing = $testing;
    }

    public function insertMiddleWare($middleWare)
    {
        $this->middleWares[] = $middleWare;
    }

    public function connect(): Client
    {
        return $this->client();
    }

    public function isConnected(): bool
    {
        return $this->client instanceof Client;
    }

    private function client(): CLient
    {
        if ($this->client) {
            return $this->client;
        }

        $this->validate();

        $handlerStack = HandlerStack::create();
        foreach ($this->middleWares as $middleWare) {
            $handlerStack->push($middleWare);
        }

        $this->client = new Client([
            'http_errors' => true,
            'handler' => $handlerStack,
            'expect' => false,
            'headers' => [
                'X-Mc-Auth' => $this->userToken,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ]
        ]);

        return $this->client;
    }

    public function get(string $endpoint)
    {
        return $this->request('GET', $endpoint);
    }

    public function post(string $endpoint, array $body)
    {
        return $this->request('POST', $endpoint, $body);
    }

    public function put(string $endpoint, array $body)
    {
        return $this->request('PUT', $endpoint, $body);
    }

    public function patch(string $endpoint, string $body)
    {
        return $this->request('PATCH', $endpoint, $body);
    }

    public function delete($endpoint)
    {
        return $this->request('DELETE', $endpoint);
    }

    public function request(string $method, string $endpoint, $body = null)
    {
        $response = $this->client()->send(
            new Request($method, $this->formatUrl($endpoint), [], $body)
        );
        return $this->parseResponse($response);
    }

    private function parseResponse(Response $response)
    {
        $response->getBody()->rewind();
        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Add user_id and blog_id to the URL as part of the authentication.
     */
    private function formatUrl(string $url): string
    {
        $baseUri = $this->isTesting() ? $this->stagingApiUrl : $this->apiUrl;

        return add_query_arg([
            'user_id' => $this->userId,
            'blog_id' => $this->blogId,
        ], trailingslashit($baseUri) . $url);
    }

    /**
     * Validate if all prerequisites are met to use the client. We need at least
     * the user token, user ID and blog ID to be set before we can make any
     * requests.
     * @throws \InvalidArgumentException
     */
    private function validate(): void
    {
        if (empty($this->userToken)) {
            throw new \InvalidArgumentException('User token is required to connect to Metricool API.');
        }

        if (empty($this->userId)) {
            throw new \InvalidArgumentException('User ID is required to connect to Metricool API.');
        }

        if (empty($this->blogId)) {
            throw new \InvalidArgumentException('Blog ID is required to connect to Metricool API.');
        }
    }
}