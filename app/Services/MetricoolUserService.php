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
    private MetricoolApi $metricool;

    public function __construct(MetricoolApi $metricool)
    {
        $this->metricool = $metricool;
        $this->user = get_option(self::METRICOOL_USER_OPTION, null);
    }

    public function update(): self
    {
        try {
            $user = $this->metricool->user()->get();
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

    /**
     * Returns a serialized version of the account of this user to be used in the front-end
     */
    public function accountData(): array
    {
        return [
            'user_id' => $this->metricool->getUserId(),
            'blog_id' => $this->metricool->getBlogId(),
            'is_premium' => $this->isPremium(),
            'user' => $this->getUser(),
        ];
    }
}
