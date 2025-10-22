<?php

namespace Metricool\Http\Endpoints;

use Metricool\App;
use Metricool\Http\Endpoints\Responses\RealtimeResponse;
use Metricool\Interfaces\SingleEndpointInterface;
use Metricool\Traits\HasAllowlistControl;
use Metricool\Traits\HasRestAccess;

class RealtimeEndpoint implements SingleEndpointInterface
{
    use HasRestAccess;
    use HasAllowlistControl;

    const ROUTE = 'realtime';

    /**
     * Only enable this endpoint if the user has access to the admin area and
     * the user has saved a user token, - ID and blog ID.
     */
    public function enabled(): bool
    {
        return $this->adminAccessAllowed() && App::provide('client')->hasAuthentication();
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
     * Build the specific Realtime response for the endpoint. This is mainly
     * used in the plugin Dashboard. Building it server side prevents client-side complexity.
     */
    public function callback(\WP_REST_Request $request): \WP_REST_Response
    {
        try {
            $response = $this->buildResponse($request);
        } catch (\Exception $e) {
            return $this->sendHttpErrorResponse(esc_html__('Failed to load Realtime data', 'metricool'), $e->getMessage());
        }

        return $this->sendHttpResponse($response);
    }

    /**
     * Build the specific Analytics response for the endpoint. This is mainly
     * used in the plugin Dashboard to reflect non-realtime statistics.
     * Building it server side prevents client-side complexity.
     */
    private function buildResponse(\WP_REST_Request $request): array
    {
        $realtimeModule = App::provide('client')->realtime();

        // Get our data
        $pageViews = $realtimeModule->pageViewsPerHour()->get();
        $sessions = $realtimeModule->sessions()->get();

        // Build the response from our data
        $response = new RealtimeResponse();
        $response->addMetric('pageViews', esc_html__('Page views', 'metricool'), $pageViews);
        $response->addMetric('visitors', esc_html__('Visitors', 'metricool'), $sessions['timeline'], false);

        return $response->body();
    }
}