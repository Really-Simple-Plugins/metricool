<?php

declare(strict_types=1);

namespace Metricool\Features\Onboarding\Endpoints;

use Metricool\Traits\HasRestAccess;
use Metricool\Traits\HasAllowlistControl;
use Metricool\Support\Validation\Validator;
use Metricool\Interfaces\SingleEndpointInterface;
use Metricool\Features\Onboarding\OnboardingService;
use Metricool\Features\Onboarding\Services\CreateAccountService;
use Metricool\Features\Onboarding\Exceptions\CreateAccountException;

final class CreateAccountEndpoint implements SingleEndpointInterface
{
    use HasRestAccess;
    use HasAllowlistControl;

    public const ROUTE = 'onboarding/create_account';

    private CreateAccountService $accounts;
    private OnboardingService $onboarding;

    public function __construct(CreateAccountService $accounts, OnboardingService $onboarding)
    {
        $this->accounts = $accounts;
        $this->onboarding = $onboarding;
    }

    /**
     * @inheritDoc
     */
    public function isEnabled(): bool
    {
        return $this->userCanManage();
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
            'methods' => 'POST',
            'callback' => [$this, 'callback'],
        ];
    }

    /**
     * Create a new Metricool account.
     *
     *     POST /wp-json/metricool/v1/onboarding/create_account
     */
    public function callback(\WP_REST_Request $request): \WP_REST_Response
    {
        $validated = Validator::validate($request->get_params(), [
            'email' => 'required|email',
            'password' => 'required|string',
            'captcha' => 'required|string',
            'terms' => 'accepted',
            'marketing' => 'boolean',
        ]);

        // Attempt to create the account
        try {
            $this->accounts->createAccount(
                $validated['captcha'],
                $validated['email'],
                $validated['password'],
                rest_sanitize_boolean($validated['marketing'] ?? false)
            );
        } catch (CreateAccountException $e) {
            return $this->sendHttpErrorResponse($e->getMessage(), ['reason' => $e->getReason()], $e->getCode());
        }

        return $this->sendHttpResponse($this->onboarding->onboardedState());
    }
}
