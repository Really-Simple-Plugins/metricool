<?php
namespace Metricool\Features\Onboarding;

use Metricool\App;
use Metricool\Http\ApiClient;
use Metricool\Helpers\Storage;
use Metricool\Utility\StringUtility;
use Metricool\Exceptions\ApiException;
use Metricool\Interfaces\FeatureInterface;
use Metricool\Exceptions\RestDataException;

class OnboardingController implements FeatureInterface
{
    private OnboardingService $service;

    public function __construct(OnboardingService $service)
    {
        $this->service = $service;
    }

    public function register()
    {
        add_filter('metricool_rest_routes', [$this, 'addRoutes']);
    }

    /**
     * Add onboarding routes to the existing routes of our plugin
     */
    public function addRoutes(array $routes): array
    {
        $routes['onboarding/finish_onboarding'] = [
            'methods' => 'POST',
            'callback' => [$this, 'finishOnboarding'],
        ];

        $routes['onboarding/retry_onboarding'] = [
            'methods' => 'POST',
            'callback' => [$this, 'retryOnboarding'],
        ];

        return $routes;
    }

    /**
     * Method is used to finish the onboarding process. It is called when the
     * user has completed the onboarding process and wants to finish it.
     */
    public function finishOnboarding(\WP_REST_Request $request): \WP_REST_Response
    {
        $code = 200;
        $message = esc_html__('Successfully finished onboarding!', 'metricool');

        $success = $this->service->setOnboardingCompleted();
        if (!$success) {
            $message = esc_html__('An error occurred while finishing the onboarding process', 'metricool');
            $code = 500;
        }

        return $this->service->sendHttpResponse([], $success, $message, $code);
    }

    /**
     * Method is used to retry the onboarding process. It is called when the
     * user has completed the onboarding process and wants to retry it.
     */
    public function retryOnboarding(\WP_REST_Request $request): \WP_REST_Response
    {
//        $success = $this->service->delete_all_options(); // todo
        $message = esc_html__('Successfully removed all previous data.', 'metricool');

        if (!$success) {
            $message = esc_html__('An error occurred while trying to remove previous data.', 'metricool');
        }

        return $this->service->sendHttpResponse([], $success, $message);
    }
}