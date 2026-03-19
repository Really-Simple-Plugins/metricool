<?php

declare(strict_types=1);

namespace Metricool\Features\Onboarding;

use GuzzleHttp\Exception\GuzzleException;
use Metricool\Features\Onboarding\Exceptions\BrandAccessDeniedException;
use Metricool\Http\Metricool\MetricoolApi;
use Metricool\Services\TrackingScriptService;

class OnboardingService
{
    private MetricoolApi $api;
    private TrackingScriptService $tracking;

    public function __construct(MetricoolApi $api, TrackingScriptService $tracking)
    {
        $this->api = $api;
        $this->tracking = $tracking;
    }

    public function state(): array
    {
        return [
            'completed' => $this->isOnboardingCompleted(),
            'authenticated' => $this->api->hasUserToken(),
            'blog_id_selected' => $this->api->hasBlogId(),
            'forced_login' => $this->isFromLegacyPlugin(),
        ];
    }

    public function isOnboardingCompleted(): bool
    {
        return $this->api->hasUserToken()
            && $this->api->hasBlogId()
            && $this->api->hasUserId();
    }

    /**
     * Set the onboarding as completed in the general options without autoload
     */
    public function setOnboardingCompleted(): void
    {
        // set the timestamp
        update_option('metricool_onboarding_completed_unix_timestamp', time(), false);
        // set completed
        update_option('metricool_onboarding_completed', true, false);
    }

    public function isFromLegacyPlugin(): bool
    {
        return (bool) get_option('metricool_from_legacy_plugin', false);
    }

    /**
     * Automatically find the blog from the connected brand and try to retrieve
     * the necessary onboarding information
     */
    public function findAndRetrieveBlogInfo(): bool
    {
        try {
            $brands = $this->api->brands()->all();
        } catch (GuzzleException $e) {
            return false;
        }

        if (empty($brands)) {
            return false;
        }

        try {
            $this->storeBlogInfo((string) $brands[0]['id']);
        } catch (GuzzleException | BrandAccessDeniedException $e) {
            return false;
        }

        return true;
    }

    /**
     * Store the necessary onboarding information from the Metricool brand
     *
     * @throws BrandAccessDeniedException when the current user has no access to the brand
     * @throws GuzzleException when the Metricool API request fails
     */
    public function storeBlogInfo(string $blogId): bool
    {
        // Attempt to get the brand information from the API, checks if the user can access it
        try {
            $brand = $this->api->brands()->get($blogId);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            // If user has no access to the brand, return an exception
            if ($e->getResponse()->getStatusCode() === 403) {
                throw new BrandAccessDeniedException();
            }
        }

        // Store the blog id
        if (isset($brand['id'])) {
            $this->api->storeBlogId((string) $brand['id']);
        } else {
            throw new \RuntimeException('Something went wrong.');
        }

        // Store the tracking hash
        if (! empty($brand['hash'])) {
            $this->tracking->storeTrackingHash((string) $brand['hash']);
        }

        return true;
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
