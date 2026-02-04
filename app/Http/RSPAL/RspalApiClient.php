<?php

namespace Metricool\Http\RSPAL;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Metricool\Support\Helpers\Storages\EnvironmentConfig;

class RspalApiClient
{
    /**
     * The Guzzle HTTP client for making API requests
     */
    protected Client $client;

    /**
     * The base endpoint URL for the brand API
     */
    private string $baseEndpoint;

    /**
     * The default headers to send with each request
     */
    private array $headers = [
        'Content-Type' => 'application/json',
        'Accept' => 'application/json',
    ];

    protected EnvironmentConfig $env;

    public function __construct(EnvironmentConfig $env)
    {
        $this->env = $env;
        $this->baseEndpoint = $this->baseEndpoint();
        $this->client = $this->client();
    }

    /**
     * @throws GuzzleException
     */
    public function installation(): RspalApiResponse
    {
        return $this->request('installation/create', [
            'headers' => $this->headers()
        ], 'post');
    }

    /**
     * @throws GuzzleException
     */
    public function signUp(array $data, array $headers = []): RspalApiResponse
    {
        return $this->request('v2/integrations/wp-plugin/users/sign-ups', [
            'json' => $data,
            'headers' => $this->headers($headers)
        ], 'post');
    }

    /**
     * @throws GuzzleException
     */
    private function request(string $path, array $params = [], string $method = 'get'): RspalApiResponse
    {
        $response = $this->client->request(strtoupper($method), $this->uri($path), $params);

        return RspalApiResponse::fromResponse($response);
    }

    /**
     * Get any headers that should be sent with the request.
     */
    private function headers(array $headers = []): array
    {
        $rspalHeaders = [
            'RSPAL-PluginName' => 'Metricool',
            'RSPAL-PluginVersion' => '1.0.0',
            'RSPAL-PluginPath' => $this->getPluginPathHeader(),
            'RSPAL-Origin' => trailingslashit(site_url()),
            'RSPAL-InstallationId' => get_option('rspal_installation_id', 'unknown'),
        ];

        $rspalHeaders['RSPAL-Signature'] = $this->getInstallationSignature($rspalHeaders, $rspalHeaders['RSPAL-InstallationId']);

        return array_merge($rspalHeaders, $headers, $this->headers);
    }

    /**
     * Get the plugin path relative to the WordPress root directory.
     */
    private function getPluginPathHeader(): string
    {
        $pluginFullPath = wp_normalize_path(realpath($this->env->getString('plugin.path')));
        $wpRoot = wp_normalize_path(realpath(ABSPATH));

        return str_replace($wpRoot, '', $pluginFullPath);
    }

    /**
     * Get the full URI for the given path.
     */
    private function uri(string $path): string
    {
        return rtrim($this->baseEndpoint, '/') . '/' . ltrim($path, '/');
    }

    /**
     * Create a new Guzzle HTTP client instance.
     */
    private function client(): Client
    {
        return new Client([
            'http_errors' => false,
            'verify' => false
        ]);
    }

    /**
     * Generate the installation signature.
     */
    private function getInstallationSignature(array $format, string $id): string
    {
        return hash_hmac('sha256', json_encode($format), $id);
    }

    // todo: hide with env and package.sh
    private function baseEndpoint(): string
    {
        return $this->env->getUrl('metricool.rsp_auth_url');
    }
}