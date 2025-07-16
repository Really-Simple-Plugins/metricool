<?php

namespace Metricool\Http\Metricool\Entities;

use Metricool\Http\Metricool\MetricoolClient;
use Metricool\Http\Metricool\Traits\IsUpdatable;

class UserSettings
{
    use IsUpdatable;

    protected MetricoolClient $client;
    private string $endpoint = 'v2/settings/users/';

    private array $fillable = [
        'name',
        'lastName',
        'language',
        'timezone',
        'accountLogo',
        'headerLogo',
        'company',
        'country',
        'state',
        'address',
        'vatNumber',
        'sendToAlternativeEmail',
        'alternativeEmail',
        'marketingNotifications',
        'billEmails',
        'beta',
        'locked',
        'enabled',
        'onboarding',
        'firstDayOfTheWeek',
        'whiteLabelIntegrator',
    ];

    public function __construct(MetricoolClient $client)
    {
        $this->client = $client;
        $this->endpoint = $this->endpoint . $client->getUserId();
    }

    /**
     * @inheritDoc
     */
    protected function getFillable(): array
    {
        return $this->fillable;
    }

    public function get(): array
    {
        $response = $this->client->get($this->endpoint);
        return ($response['data'] ?? []);
    }
}