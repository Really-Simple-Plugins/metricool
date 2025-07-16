<?php

namespace Metricool\Http\Metricool;

/**
 * Facade for Metricool API entities.
 */
class MetricoolEntities
{
    protected ?MetricoolClient $client = null;

    /**
     * MetricoolEntities constructor also validates if the Facade is setup
     * correctly, see: {@see validate}
     * @throws \Exception If the connection is not valid.
     */
    public function __construct(MetricoolClient $client)
    {
        $this->client = $client;
        $this->validate();
    }

    /**
     * Easy access to the ConnectedBrands entity.
     */
    public function connectedBrands(): Entities\ConnectedBrands
    {
        return new Entities\ConnectedBrands($this->client);
    }

    /**
     * Easy access to the Subscription entity.
     */
    public function subscription(): Entities\Subscription
    {
        return new Entities\Subscription($this->client);
    }

    /**
     * Easy access to the statistic entities via the StatisticsFacade.
     */
    public function statistics(): Entities\StatisticsFacade
    {
        return new Entities\StatisticsFacade($this->client);
    }

    /**
     * Easy access to the Scheduler entity.
     */
    public function scheduler(): Entities\Scheduler
    {
        return new Entities\Scheduler($this->client);
    }

    public function userSettings(): Entities\UserSettings
    {
        return new Entities\UserSettings($this->client);
    }

    /**
     * Validate if the Entities can be used. Check for:
     * - Connection to Metricool API
     * @throws \Exception
     */
    private function validate(): void
    {
        if (!$this->client || !$this->client->isConnected()) {
            throw new \Exception('Metricool client is not connected.');
        }
    }
}