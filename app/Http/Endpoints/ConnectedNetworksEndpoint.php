<?php

declare(strict_types=1);

namespace Metricool\Http\Endpoints;

use Metricool\Bootstrap\App;
use Metricool\Traits\HasRestAccess;
use Metricool\Traits\HasAllowlistControl;
use Metricool\Interfaces\SingleEndpointInterface;
use Metricool\Http\Endpoints\Responses\ConnectedNetworksResponse;

class ConnectedNetworksEndpoint implements SingleEndpointInterface
{
    use HasRestAccess;
    use HasAllowlistControl;

    public const ROUTE = 'connected_networks';

    /**
     * Only enable this endpoint if the user has access to the admin area and
     * the user has saved a user token.
     */
    public function enabled(): bool
    {
        return $this->adminAccessAllowed() && App::getInstance()->client->hasUserToken();
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
            'methods' => \WP_REST_Server::READABLE,
            'callback' => [$this, 'callback'],
        ];
    }

    /**
     * Return the brands related to the user
     */
    public function callback(\WP_REST_Request $request): \WP_REST_Response
    {
        try {
            $response = $this->buildResponse($request);
        } catch (\Exception $e) {
            return $this->sendHttpErrorResponse(__('Failed to load brands data', 'metricool'), $e->getMessage());
        }

        return $this->sendHttpResponse($response);
    }

    /**
     * Build the specific ConnectedNetworksResponse response for the endpoint.
     * This response returns just the brand names that are connected to the user.
     * Filtering it server side prevents client-side complexity.
     */
    public function buildResponse(\WP_REST_Request $request): array
    {
        $connectedBrand = App::getInstance()->client->connectedBrands()->get();
        $response = new ConnectedNetworksResponse($connectedBrand);

        return $response->body();
    }
}
