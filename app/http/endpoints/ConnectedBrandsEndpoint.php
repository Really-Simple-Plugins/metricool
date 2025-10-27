<?php

namespace Metricool\Http\Endpoints;

use Metricool\App;
use Metricool\Interfaces\SingleEndpointInterface;
use Metricool\Traits\HasAllowlistControl;
use Metricool\Traits\HasRestAccess;

class ConnectedBrandsEndpoint implements SingleEndpointInterface
{
    use HasRestAccess;
    use HasAllowlistControl;

    public const ROUTE = 'connected_brands';

    /**
     * Only enable this endpoint if the user has access to the admin area and
     * the user has saved a user token.
     */
    public function enabled(): bool
    {
        return $this->adminAccessAllowed() && App::provide('client')->hasUserToken();
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
            $response = App::provide('client')->connectedBrands()->get();
        } catch (\Throwable $e) {
            echo '<pre>';
            var_dump($e->getMessage()); // todo
            exit();
        }

        return $this->sendHttpResponse($response);
    }
}