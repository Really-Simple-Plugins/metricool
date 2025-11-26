<?php

declare(strict_types=1);

namespace Metricool\Http\Endpoints;

use Metricool\Bootstrap\App;
use Metricool\Traits\HasRestAccess;
use Metricool\Traits\HasAllowlistControl;
use Metricool\Interfaces\SingleEndpointInterface;

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
            $response = App::getInstance()->client->connectedBrands()->get();
        } catch (\Throwable $e) {
            echo '<pre>';
            var_dump($e->getMessage()); // todo
            exit();
        }

        return $this->sendHttpResponse($response);
    }
}
