<?php

declare(strict_types=1);

namespace Metricool\Features\UserSettings\Endpoints;

use Metricool\Traits\HasRestAccess;
use Metricool\Interfaces\MultiEndpointInterface;
use Metricool\Features\UserSettings\UserSettingsService;
use Metricool\Features\UserSettings\UserSettingsResponse;
use Metricool\Features\UserSettings\Exceptions\StorageSubmitException;
use Metricool\Features\UserSettings\Exceptions\ValidationFailedExceptions;

final class UserSettingsEndpoints implements MultiEndpointInterface
{
    use HasRestAccess;

    private UserSettingsService $service;

    public function __construct(UserSettingsService $service)
    {
        $this->service = $service;
    }

    /**
     * Endpoints are guarded by the {@see UserSettingsFeature}, which only
     * registers these endpoints when the current user can manage the plugin.
     * So this class does not need its own capability check.
     */
    public function isEnabled(): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function registerRoutes(): array
    {
        $routeParameters = [
            'methods' => \WP_REST_Server::READABLE . ', ' . \WP_REST_Server::EDITABLE,
            'callback' => [$this, 'handleUserSettings'],
        ];

        return [
            'user_settings' => $routeParameters,
            'user_settings/(?P<section>[^/]+)' => $routeParameters,
        ];
    }

    /**
     * Handle the REST API request, routing to the appropriate method to the
     * correct method. Only allows GET, POST, PUT, and PATCH.
     */
    public function handleUserSettings(\WP_REST_Request $request): \WP_REST_Response
    {
        switch ($request->get_method()) {
            case \WP_REST_Server::READABLE:
                return $this->getUserSettings($request);
            case 'POST':
            case 'PUT':
            case 'PATCH':
                return $this->storeUserSettings($request);
            default:
                return $this->sendHttpResponse([], false, 'Method not allowed', 405);
        }
    }

    /**
     * Retrieve user settings, optionally filtered by section. Sends an error
     * response if something goes wrong.
     */
    protected function getUserSettings(\WP_REST_Request $request): \WP_REST_Response
    {
        try {
            $settings = $this->service->getSettingsResponse(
                $request->get_param('section')
            );
        } catch (\Exception $e) {
            return $this->sendHttpErrorResponse(__('Failed to retrieve settings', 'metricool'), $e->getMessage());
        }

        return $this->sendHttpResponse($settings);
    }

    /**
     * Store user settings. Sends an error response if something goes wrong.
     */
    protected function storeUserSettings(\WP_REST_Request $request): \WP_REST_Response
    {
        try {
            $settings = $request->get_params();
            $updatedSettings = $this->service->storeSettings($settings, $request);
        } catch (ValidationFailedExceptions $e) {
            return $this->sendHttpResponse($e->getErrors(), false, __('Validation failed', 'metricool'), 422);
        } catch (StorageSubmitException $e) {
            return $this->sendHttpErrorResponse($e->getMessage(), $e->getErrors(), 502);
        } catch (\Exception $e) {
            return $this->sendHttpErrorResponse(__('An unknown error occurred. Failed to update the settings!', 'metricool'), $e->getMessage());
        }

        $response = new UserSettingsResponse($updatedSettings);

        return $this->sendHttpResponse(
            $response->parse()->get()
        );
    }
}
