<?php

declare(strict_types=1);

namespace Metricool\Features\Onboarding\Endpoints;

use Metricool\Features\Onboarding\Services\OAuthService;
use Metricool\Interfaces\SingleEndpointInterface;
use Metricool\Traits\HasAllowlistControl;
use Metricool\Traits\HasRestAccess;

final class OAuthRedirectEndpoint implements SingleEndpointInterface
{
    use HasRestAccess;
    use HasAllowlistControl;

    public const ROUTE = 'onboarding/oauth_redirect';

    private OAuthService $oauth;

    public function __construct(OAuthService $oauth)
    {
        $this->oauth = $oauth;
    }

    /**
     * @inheritDoc
     */
    public function enabled(): bool
    {
        return $this->userCanManage();
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
            'methods' => 'GET',
            'callback' => [$this, 'callback'],
        ];
    }

    /**
     * Return the Metricool OAuth authorize URL.
     *
     *     GET /wp-json/metricool/v1/onboarding/oauth_redirect
     */
    public function callback(\WP_REST_Request $request): \WP_REST_Response
    {
        if (is_ssl() === false) {
            return $this->sendHttpErrorResponse(__('HTTPS is required to be able to authorize this website.', 'metricool'), [], 400);
        }

        return $this->sendHttpResponse([
            'redirect_url' => $this->oauth->getAuthorizationUrl(),
        ]);
    }
}
