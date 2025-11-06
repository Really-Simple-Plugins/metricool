<?php

namespace Metricool\Features\UserSettings;

use Metricool\Traits\HasAllowlistControl;
use Metricool\Traits\HasRestAccess;

class UserSettingsEndpoint
{
    use HasRestAccess;
    use HasAllowlistControl;

    private UserSettingsService $service;

    public function __construct(UserSettingsService $service)
    {
        $this->service = $service;
    }

    public function register()
    {
        add_filter('metricool_rest_routes', [$this, 'addRoutes']);
    }

    /**
     * Add the task routes to the REST API.
     */
    public function addRoutes(array $routes): array
    {
//        if ($this->adminAccessAllowed() === false) {
//            return $routes;
//        }

        $routeParameters = [
            'methods' => \WP_REST_Server::READABLE . ', ' . \WP_REST_Server::EDITABLE,
            'callback' => [$this, 'callback'],
            'permission_callback' => '__return_true',
        ];

        $routes['user_settings'] = $routeParameters;
        $routes['user_settings/(?P<section>[^/]+)'] = $routeParameters;

        return $routes;
    }


    public function callback(\WP_REST_Request $request): \WP_REST_Response
    {
        switch ($request->get_method()) {
            case \WP_REST_Server::READABLE:
                return $this->getUserSettings($request);
            case 'POST':
            case 'PUT':
            case 'PATCH':
                return $this->updateUserSettings($request);
            default:
                return $this->sendHttpResponse([], false, 'Method not allowed', 405);
        }
    }

    protected function getUserSettings(\WP_REST_Request $request): \WP_REST_Response
    {
        $section = $request->get_param('section');

        try {
            $settings = !empty($section)
                ? $this->service->getSettingsForSection($section)
                : $this->service->getAllSettings();
        } catch (\Exception $e) {
            return $this->sendHttpErrorResponse(__('Failed to retrieve settings', 'metricool'), $e->getMessage(), 500);
        }

        return $this->sendHttpResponse($settings);
    }


    protected function updateUserSettings(\WP_REST_Request $request): \WP_REST_Response
    {
        $params = $request->get_params();

        try {
            $updatedSettings = $this->service->updateSettings($params, $request);
        } catch (\Exception $e) {
            return $this->sendHttpErrorResponse(__('Failed to update settings', 'metricool'), $e->getMessage(), 500);
        }

        if (is_wp_error($updatedSettings)) {
            // Validation failed, return errors
            $errors = [];
            foreach ($updatedSettings->get_error_codes() as $code) {
                $messages = $updatedSettings->get_error_messages($code);
                $errorData = $updatedSettings->get_error_data($code);

                foreach ($messages as $message) {
                    $errors[$errorData['field']] = [
                        'message' => $message
                    ];
                }
            }
            return $this->sendHttpResponse(['errors' => $errors], true, 'Validation failed', 422);
        }

        return $this->sendHttpResponse($updatedSettings);
    }
}