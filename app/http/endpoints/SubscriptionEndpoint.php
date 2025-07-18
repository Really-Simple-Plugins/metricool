<?php
namespace Metricool\Http\Endpoints;

use Metricool\App;
use Metricool\Traits\HasRestAccess;
use Metricool\Traits\HasAllowlistControl;
use Metricool\Interfaces\SingleEndpointInterface;

class SubscriptionEndpoint implements SingleEndpointInterface
{
    use HasRestAccess;
    use HasAllowlistControl;

    const ROUTE = 'subscription';

    /**
     * Only enable this endpoint if the user has access to the admin area and
     * the user has saved a user token and ID.
     */
    public function enabled(): bool
    {
        return $this->adminAccessAllowed() && App::provide('client')->hasUserToken() && App::provide('client')->hasUserId();
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
     * Return the subscription data related to the user
     */
    public function callback(\WP_REST_Request $request): \WP_REST_Response
    {
        try {
            $response = App::provide('client')->subscription()->get();
        } catch (\Throwable $e) {
            echo '<pre>';
            var_dump($e->getMessage()); // todo
            exit();
        }

        return $this->sendHttpResponse($response);
    }
}