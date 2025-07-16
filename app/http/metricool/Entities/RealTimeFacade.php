<?php

namespace Metricool\Http\Metricool\Entities;

use Metricool\Http\Metricool\MetricoolClient;

class RealTimeFacade
{
    protected MetricoolClient $client;

    public function __construct(MetricoolClient $client)
    {
        $this->client = $client;
    }

    public function current(): RealTimeStatistics
    {
        return new RealTimeStatistics($this->client, 'values');
    }

    public function sessions(): RealTimeStatistics
    {
        return new RealTimeStatistics($this->client, 'sessions');
    }

    public function pageViewsPerHour(): RealTimeStatistics
    {
        return new RealTimeStatistics($this->client, 'pvperhour');
    }

    public function pageViews(): RealTimeStatistics
    {
        return new RealTimeStatistics($this->client, 'distribution/currentpageviews');
    }

    public function referers(): RealTimeStatistics
    {
        return new RealTimeStatistics($this->client, 'distribution/referers');
    }

    public function countries(): RealTimeStatistics
    {
        return new RealTimeStatistics($this->client, 'distribution/countries');
    }

    public function sources(): RealTimeStatistics
    {
        return new RealTimeStatistics($this->client, 'distribution/sources');
    }

}