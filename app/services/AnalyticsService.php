<?php

namespace Metricool\Services;

use Carbon\Carbon;
use Metricool\Utility\ArrayUtility;
use Metricool\Http\Metricool\Entities\TimelineStatistics;

class AnalyticsService
{
    public const TREND_UP = 'up';
    public const TREND_DOWN = 'down';
    public const TREND_STABLE = 'stable';

    /**
     * Method returns the trend for the given statistic module based on the
     * given filters. To be able to calculate the trend the filters need
     * at least a start and an end date in Ymd format. Otherwise, a
     * 'stable' trend is returned,
     */
    public function getTrend(TimelineStatistics $statistic, array $filters): string
    {
        $cacheName = get_class($statistic) . ':' . $statistic->getMetric() . '#' . md5(json_encode($filters));
        if ($cache = wp_cache_get($cacheName, 'metricool')) {
            return $cache;
        }

        $trend = self::TREND_STABLE;

        // Check for mandatory period filters, return fallback if none provided
        if (empty($filters) || empty($filters['start']) || empty($filters['end'])) {
            return $trend;
        }

        try {
            // Fetch data for current period and the previous period
            $currentPeriodResponse = $statistic->filter($filters)->get();
            $previousPeriodResponse = $statistic->filter(
                $this->getPreviousPeriodFilters($filters)
            )->get();
        } catch (\Throwable $e) {
            return $trend;
        }

        // Compare the sum of both periods
        $statisticSumCurrentPeriod = ArrayUtility::sumValues(array_column($currentPeriodResponse, 1));
        $statisticSumPreviousPeriod = ArrayUtility::sumValues(array_column($previousPeriodResponse, 1));

        if ($statisticSumCurrentPeriod > $statisticSumPreviousPeriod){
            $trend = self::TREND_UP;
        }

        if ($statisticSumCurrentPeriod < $statisticSumPreviousPeriod){
            $trend = self::TREND_DOWN;
        }

        wp_cache_set($cacheName, $trend, 'metricool');
        return $trend;
    }

    /**
     * Method returns filters for the previous period based on the given
     * filters. A period is defined as the difference between start and
     * end date.
     */
    public function getPreviousPeriodFilters(array $filters): array
    {
        if (empty($filters) || empty($filters['start']) || empty($filters['end'])) {
            throw new \InvalidArgumentException("Filters 'start' and 'end' are required to get the previous period");
        }

        $start = Carbon::createFromFormat('Ymd', $filters['start']);
        $end = Carbon::createFromFormat('Ymd', $filters['end']);

        // We do +1 to end the previous period one day before the current period
        $diffInDays = $start->diffInDays($end) + 1;

        $previousStart = $start->copy()->subDays($diffInDays);
        $previousEnd = $end->copy()->subDays($diffInDays);

        return [
            'start' => $previousStart->format('Ymd'),
            'end'   => $previousEnd->format('Ymd'),
        ];
    }

}