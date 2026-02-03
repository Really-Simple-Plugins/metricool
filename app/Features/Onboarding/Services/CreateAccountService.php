<?php

namespace Metricool\Features\Onboarding\Services;

use GuzzleHttp\Exception\GuzzleException;
use Metricool\Http\RSPAL\RspalApiClient;
use Metricool\Http\RSPAL\RspalApiResponse;

class CreateAccountService
{
    public const INSTALLATION_ID_OPTION = 'rspal_installation_id';

    private RspalApiClient $apiClient;

    public function __construct(RspalApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    /**
     * @throws GuzzleException
     */
    public function createAccount(array $data, string $captcha): RspalApiResponse
    {
        if (!$this->hasInstallationId()) {
            $this->createInstallationId();
        }

        return $this->apiClient->signUp($data, ['RSPAL-RecaptchaV3Token' => $captcha]);
    }

    /**
     * @throws GuzzleException
     */
    private function createInstallationId(): void
    {
        $response = $this->apiClient->installation();

        $this->setInstallationId($response->data->uuid);
    }

    private function setInstallationId(string $installationId): void
    {
        $installation = update_option(self::INSTALLATION_ID_OPTION, $installationId);

        if (!$installation) {
            throw new \RuntimeException('Failed to store InstallationID');
        }
    }

    private function hasInstallationId(): bool
    {
        return self::getInstallationId() !== 'unknown';
    }

    private function getInstallationId(): string
    {
        return get_option(self::INSTALLATION_ID_OPTION, 'unknown');
    }
}
