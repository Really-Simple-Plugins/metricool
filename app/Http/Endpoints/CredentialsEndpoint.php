<?php

declare(strict_types=1);

namespace Metricool\Http\Endpoints;

use GuzzleHttp\Exception\GuzzleException;
use Metricool\Http\Metricool\MetricoolApi;
use Metricool\Interfaces\SingleEndpointInterface;
use Metricool\Traits\HasAllowlistControl;
use Metricool\Traits\HasRestAccess;

class CredentialsEndpoint implements SingleEndpointInterface
{
    use HasRestAccess;
    use HasAllowlistControl;

    public const ROUTE = 'credentials';

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
     * the user has saved a user token.
     */
    public function enabled(): bool
    {
        return $this->adminAccessAllowed();
    }

    /**
     * @inheritDoc
     */
    public function registerArguments(): array
    {
        return [
            'methods' => \WP_REST_Server::EDITABLE,
            'callback' => [$this, 'callback'],
            'middleware' => ['metricool:auth'],
        ];
    }

    /**
     * Method will dynamically request the requested statistic. If the metric
     * is filterable and filters are provided, it will apply them before
     * retrieving the data.
     * @example /wp-json/metricool/v1/analytics
     */
    public function callback(\WP_REST_Request $request): \WP_REST_Response
    {
        $password = (string) $request->get_param('password');
        $newPassword = (string) $request->get_param('newPassword');

        // Validation
        if (empty($password) || empty($newPassword)) {
            return $this->sendHttpErrorResponse(__('Validation failed', 'metricool'));
        }

        // Update the user password
        try {
            $this->metricoolApi->userCredentials()
                ->updatePassword($password, $newPassword);
        } catch (GuzzleException $e) {
            return $this->sendHttpErrorResponse(__('Something went wrong.', 'metricool'), $e->getMessage(), $e->getCode());
        }

        return $this->sendHttpResponse(['success' => true]);
    }
}
