<?php

namespace Metricool\Http\Endpoints;

use Metricool\App;
use Metricool\Interfaces\SingleEndpointInterface;
use Metricool\Traits\HasAllowlistControl;
use Metricool\Traits\HasRestAccess;

class UserSettingsEndpoint implements SingleEndpointInterface
{
    use HasRestAccess;
    use HasAllowlistControl;

    public const ROUTE = 'user_settings';

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
            'methods' => \WP_REST_Server::READABLE, \WP_REST_Server::EDITABLE,
            'callback' => [$this, 'callback'],
        ];
    }

    /**
     * Return the brands related to the user
     */
    public function callback(\WP_REST_Request $request): \WP_REST_Response
    {
        switch ($request->get_method()) {
            case \WP_REST_Server::READABLE:
                return $this->getUserSettings();
            case 'POST':
            case 'PUT':
            case 'PATCH':
                return $this->updateUserSettings($request);
            default:
                return $this->sendHttpResponse([], false, 'Method not allowed', 405);
        }
    }

    /**
     * Get the user settings from the Metricool API.
     */
    protected function getUserSettings(): \WP_REST_Response
    {
        try {
            $response = App::provide('client')->userSettings()->get();
        } catch (\Throwable $e) {
            echo '<pre>';
            var_dump($e->getMessage()); // todo
            exit();
        }

        return $this->sendHttpResponse($response);
    }

    /**
     * Update the user settings in the Metricool API.
     */
    protected function updateUserSettings(\WP_REST_Request $request): \WP_REST_Response
    {
        $body = $request->get_json_params();

        // todo - maybe return changed fields?

        try {
            $response = App::provide('client')->userSettings()->update($body);
        } catch (\Throwable $e) {
            echo '<pre>';
            var_dump($e->getMessage()); // todo
            exit();
        }

        return $this->sendHttpResponse($response);
    }
}