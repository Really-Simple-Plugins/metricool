<?php

namespace Metricool\Http\Metricool;

/**
 * Facade for Metricool API entities.
 */
class MetricoolEntities
{
    protected ?MetricoolClient $client = null;

    public function __construct(MetricoolClient $client)
    {
        $this->client = $client;
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
     * Easy access to the real time entities via the RealtimeFacade.
     */
    public function realtime(): Entities\RealtimeFacade
    {
        return new Entities\RealtimeFacade($this->client);
    }

    /**
     * Easy access to the UserSettings entity.
     */
    public function userSettings(): Entities\UserSettings
    {
        return new Entities\UserSettings($this->client);
    }

    /**
     * This magic method is called when a method is requested that does not
     * exist on this class. It will try to call the method on the
     * MetricoolClient instance, if not found, it will throw an exception.
     * @throws \BadMethodCallException
     */
    public function __call(string $name, array $arguments)
    {
        if (method_exists($this->client, $name)) {
            return $this->client->{$name}(...$arguments);
        }

        throw new \BadMethodCallException("Method {$name} does not exist on MetricoolClient.");
    }
}