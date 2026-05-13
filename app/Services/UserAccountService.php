<?php

declare(strict_types=1);

namespace Metricool\Services;

use GuzzleHttp\Exception\GuzzleException;
use Metricool\Http\Metricool\MetricoolApi;

class UserAccountService
{
    public const METRICOOL_USER_OPTION = 'metricool_user';
    private ?object $user;
    private MetricoolApi $api;

    public function __construct(MetricoolApi $api)
    {
        $this->api = $api;
        $this->user = get_option(self::METRICOOL_USER_OPTION, null);
    }

    public function updateUserFromApi(): void
    {
        if (!$this->api->hasAuthentication()) {
            return;
        }

        try {
            $user = (object) $this->api->get('user/settings');
        } catch (GuzzleException $e) {
            // If the request fails, we don't want to update the user data, but we also don't want to break the plugin.
            return;
        }

        $this->storeUser($user);

    }

    public function getUser(): object
    {
        return $this->user;
    }

    public function storeUser(object $user): void
    {
        $this->user = $user;
        update_option(self::METRICOOL_USER_OPTION, $user);
    }

    /**
     * Returns if the user is paid
     */
    public function isPremium(): bool
    {
        return !empty($this->user->subscription->plan)
            && $this->user->subscription->plan !== 'FREE';
    }
}
