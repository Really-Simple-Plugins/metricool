<?php

namespace Metricool\Http\Metricool\Entities;

use GuzzleHttp\Exception\GuzzleException;
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
    public function getFillable(): array
    {
        return $this->fillable;
    }

    /**
     * @throws GuzzleException
     */
    public function get(): array
    {
        $response = $this->client->get($this->endpoint);
        return ($response['data'] ?? []);
    }

    /**
     * @throws GuzzleException
     */
    public function patch(array $data): array
    {
        $endpoint = $this->endpoint . '?fields=' . implode('&fields=', array_keys($data));
        $response = $this->client->patch($endpoint, json_encode($data));

        return ($response['data'] ?? []);
    }
}