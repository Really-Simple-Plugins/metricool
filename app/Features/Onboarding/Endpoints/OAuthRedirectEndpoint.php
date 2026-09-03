<?php

declare(strict_types=1);

namespace Metricool\Features\Onboarding\Endpoints;

use WP_REST_Server;
use Metricool\Traits\HasRestAccess;
use Metricool\Interfaces\SingleEndpointInterface;
use Metricool\Features\Onboarding\Services\OAuthService;

final class OAuthRedirectEndpoint implements SingleEndpointInterface
{
    use HasRestAccess;

    public const ROUTE = 'onboarding/oauth_redirect';

    private OAuthService $oauth;

    public function __construct(OAuthService $oauth)
    {
        $this->oauth = $oauth;
    }

    /**
     * Endpoint is guarded by the {@see OnboardingFeature}, which only registers
     * this endpoint when the current user can manage the plugin. So this class
     * does not need its own capability check.
     */
    public function isEnabled(): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function registerRoute(): string
    {
        return self::ROUTE;
    }

    /**
     * @inheritDoc
     */
    public function registerArguments(): array
    {
        return [
            'methods' => WP_REST_Server::READABLE,
            'callback' => [$this, 'getOAuthRedirectUrl'],
        ];
    }

    /**
     * Return the Metricool OAuth authorize URL.
     *
     *     GET /wp-json/metricool/v1/onboarding/oauth_redirect
     */
    public function getOAuthRedirectUrl(\WP_REST_Request $request): \WP_REST_Response
    {
        if (is_ssl() === false) {
            return $this->sendHttpErrorResponse(__('HTTPS is required to be able to authorize this website.', 'metricool'), [], 400);
        }

        return $this->sendHttpResponse([
            'redirect_url' => $this->oauth->getAuthorizationUrl(),
        ]);
    }
}
