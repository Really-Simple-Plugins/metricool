<?php

namespace Metricool\Http\RSPAL;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Metricool\Support\Helpers\Storages\EnvironmentConfig;

class RspalApiClient
{
    /**
     * The option name for storing the InstallationID.
     */
    public const INSTALLATION_ID_OPTION = 'rspal_installation_id';

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
     * Sign Up Endpoint
     *
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
     * Make a request to the RSPAL API.
     *
     * @throws GuzzleException
     */
    private function request(string $path, array $params = [], string $method = 'get'): RspalApiResponse
    {

        // Request and store installationId if needed before any request
        if (!$this->hasInstallationId()) {
            $installation = $this->requestInstallation();

            $this->setInstallationId($installation->data->uuid);
        }

        $response = $this->client->request(strtoupper($method), $this->uri($path), $params);

        return RspalApiResponse::fromResponse($response);
    }

    /**
     * Request a new InstallationID from the RSPAL API.
     *
     * @throws GuzzleException
     */
    private function requestInstallation(): RspalApiResponse
    {
        $response = $this->client->request('POST', $this->uri('installation/create'), [
            'headers' => $this->headers()
        ]);

        return RspalApiResponse::fromResponse($response);
    }

    /**
     * Get the base endpoint URL for the brand API.
     */
    private function baseEndpoint(): string
    {
        return $this->env->getUrl('metricool.rsp_auth_url');
    }

    /**
     * Create a new Guzzle HTTP client instance.
     */
    private function client(): Client
    {
        return new Client([
            'http_errors' => true,
            'verify' => false,
        ]);
    }

    /**
     * Get any headers that should be sent with the request.
     */
    private function headers(array $headers = []): array
    {
        $rspalHeaders = [
            'RSPAL-PluginName' => $this->env->getString('plugin.name'),
            'RSPAL-PluginVersion' => $this->env->getString('plugin.version'),
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
     * Generate the installation signature.
     */
    private function getInstallationSignature(array $format, string $id): string
    {
        return hash_hmac('sha256', json_encode($format), $id);
    }

    /**
     * Stores the InstallationID in wp_options
     */
    private function setInstallationId(string $installationId): void
    {
        $installation = update_option(self::INSTALLATION_ID_OPTION, $installationId);

        if (!$installation) {
            throw new \RuntimeException('Failed to store InstallationID');
        }
    }

    /**
     * Check if the InstallationID has already been stored
     */
    private function hasInstallationId(): bool
    {
        return self::getInstallationId() !== 'unknown';
    }

    /**
     * Return the installationId from wp_options
     */
    private function getInstallationId(): string
    {
        return get_option(self::INSTALLATION_ID_OPTION, 'unknown');
    }
}
