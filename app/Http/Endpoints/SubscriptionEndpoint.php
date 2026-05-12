<?php

declare(strict_types=1);

namespace Metricool\Http\Endpoints;

use Metricool\Traits\HasRestAccess;
use Metricool\Traits\HasAllowlistControl;
use Metricool\Http\Metricool\MetricoolApi;
use Metricool\Interfaces\SingleEndpointInterface;

class SubscriptionEndpoint implements SingleEndpointInterface
{
    use HasRestAccess;
    use HasAllowlistControl;

    public const ROUTE = 'subscription';

    public MetricoolApi $metricoolApi;

    public function __construct(MetricoolApi $metricoolApi)
    {
        $this->metricoolApi = $metricoolApi;
    }

    /**
     * @inheritDoc
     */
    public function registerRoute(): string
    {
        return self::ROUTE;
    }

    /**
     * Only enable this endpoint if the user has access to the admin area and
     * the user has saved a user token and ID.
     */
    public function enabled(): bool
    {
        return $this->adminAccessAllowed() && $this->metricoolApi->hasAuthentication() && $this->metricoolApi->hasBlogId();
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
            $response = $this->metricoolApi->subscription()->get();
        } catch (\Exception $e) {
            return $this->sendHttpErrorResponse(__('Failed to load subscription data', 'metricool'), $e->getMessage(), $e->getCode());
        }

        return $this->sendHttpResponse($response);
    }
}
