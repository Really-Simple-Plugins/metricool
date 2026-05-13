<?php

declare(strict_types=1);

namespace Metricool\Services;

use GuzzleHttp\Exception\GuzzleException;
use Metricool\Http\Metricool\MetricoolApi;
use Metricool\Support\Helpers\Event;

class MetricoolUserService
{
    public const METRICOOL_USER_OPTION = 'metricool_user';

    private ?array $user;
    private MetricoolApi $api;

    public function __construct(MetricoolApi $api)
    {
        $this->api = $api;
        $this->user = get_option(self::METRICOOL_USER_OPTION, null);
    }

    public function update(): self
    {
        try {
            $user = $this->api->user()->get();
        } catch (GuzzleException $e) {
            // If the request fails, we don't want to update the user data, but we also don't want to break the plugin.
            return $this;
        }

        $this->storeUser($user);

        Event::dispatch(EVENT::METRICOOL_USER_UPDATED, $user);

        return $this;
    }

    public function getUser(): ?array
    {
        return $this->user;
    }

    public function storeUser(array $user): void
    {
        $this->user = $user;
        update_option(self::METRICOOL_USER_OPTION, $user);
    }

    /**
     * Returns if the user is paid
     */
    public function isPremium(): bool
    {
        return isset($this->user['subscription']['plan']['id'])
            && $this->user['subscription']['plan']['id'] !== 'FREE';
    }
}
