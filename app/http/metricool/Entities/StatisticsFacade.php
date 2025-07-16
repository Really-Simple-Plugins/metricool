<?php

namespace Metricool\Http\Metricool\Entities;

use Metricool\Http\Metricool\MetricoolClient;

class StatisticsFacade
{
    protected MetricoolClient $client;

    public function __construct(MetricoolClient $client)
    {
        $this->client = $client;
    }

    public function countries(): DistributionStatistics
    {
        return new DistributionStatistics($this->client, 'country');
    }

    public function referers(): DistributionStatistics
    {
        return new DistributionStatistics($this->client, 'referers');
    }

    public function sources(): DistributionStatistics
    {
        return new DistributionStatistics($this->client, 'sources');
    }

    public function pageViews(): TimelineStatistics
    {
        return new TimelineStatistics($this->client, 'PageViews');
    }

    public function visits(): TimelineStatistics
    {
        return new TimelineStatistics($this->client, 'SessionsCount');
    }

    public function visitors(): TimelineStatistics
    {
        return new TimelineStatistics($this->client, 'Visitors');
    }

    public function posts(): TimelineStatistics
    {
        return new TimelineStatistics($this->client, 'DailyPosts');
    }

    public function comments(): TimelineStatistics
    {
        return new TimelineStatistics($this->client, 'DailyComments');
    }

}