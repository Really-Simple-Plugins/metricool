<?php

declare(strict_types=1);

namespace Metricool\Features\Onboarding\Endpoints;

use Throwable;
use WP_REST_Server;
use Metricool\Traits\HasRestAccess;
use Metricool\Interfaces\SingleEndpointInterface;
use Metricool\Features\Onboarding\OnboardingService;
use Metricool\Support\Helpers\Storages\EnvironmentConfig;
use Metricool\Features\Onboarding\Exceptions\BrandAccessDeniedException;

final class FinishOnboardingEndpoint implements SingleEndpointInterface
{
    use HasRestAccess;

    public const ROUTE = 'onboarding/finish_onboarding';

    private OnboardingService $onboarding;
    private EnvironmentConfig $env;

    public function __construct(OnboardingService $onboarding, EnvironmentConfig $env)
    {
        $this->onboarding = $onboarding;
        $this->env = $env;
    }

    /**
     * Endpoint is guarded by the {@see OnboardingFeature}, which only registers
     * this endpoint when the current user can manage the plugin. So this class
     * does not need its own capability check.
     */
    public function isEnabled(): bool
    {
        return true;
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
            'methods' => WP_REST_Server::CREATABLE,
            'callback' => [$this, 'finishOnboarding'],
        ];
    }

    /**
     * Finish onboarding after connecting a brand.
     *
     *     POST /wp-json/metricool/v1/onboarding/finish_onboarding
     */
    public function finishOnboarding(\WP_REST_Request $request): \WP_REST_Response
    {
        $blogId = (string) $request->get_param('blogId');

        if (empty($blogId)) {
            return $this->sendHttpResponse($this->onboarding->onboardedState());
        }

        // Store the blogId if it was provided by the client, to retrieve the necessary blog information
        try {
            $this->onboarding->finalizeOnboarding($blogId);
        } catch (BrandAccessDeniedException $e) {
            return $this->sendHttpErrorResponse(
                $e->getLocalizedMessage(),
                [],
                403
            );
        } catch (Throwable $e) {
            return $this->sendHttpErrorResponse(wp_kses_post(sprintf(
                /* translators: %1$s is opening link and %2$s is closing link */
                __('Something went wrong. Please try again or %1$sleave a support message%2$s.', 'metricool'),
                '<a href="' . $this->env->get('frontend.trusted_urls.new_support_ticket') . '" target="_blank">',
                '</a>',
            )), ['data' => $e->getMessage()], $e->getCode() ?: 500);
        }

        return $this->sendHttpResponse($this->onboarding->onboardedState());
    }
}
