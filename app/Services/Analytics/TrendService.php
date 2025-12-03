<?php

namespace Metricool\Services\Analytics;

use Carbon\Carbon;
use Metricool\Support\Helpers\Collection;
use Metricool\Http\Metricool\Entities\TimelineStatistics;

class TrendService
{
    public const TREND_UP = 'up';
    public const TREND_DOWN = 'down';
    public const TREND_STABLE = 'stable';

    /**
     * Method returns the trend for the given statistic module based on the
     * given filters. To be able to calculate the trend the filters need
     * at least a start and an end date in Ymd format. Otherwise, a
     * 'stable' trend is returned
     *
     * @param array $filters Optional filters to override the filters used on
     * the TimelineStatistics instance.  Must contain 'start' and 'end' keys
     * in Ymd format.
     */
    public function getTrend(TimelineStatistics $statistic, Collection $currentStatistics, array $filters = []): string
    {
        $cacheName = get_class($statistic) . ':' . $statistic->getMetric() . '#' . md5(json_encode($filters));
        if ($cache = wp_cache_get($cacheName, 'metricool')) {
            return $cache;
        }

        $trend = self::TREND_STABLE;

        // Check for mandatory start- and end-filter. If not present, use
        // the filters used on the given statistic instance.
        if (empty($filters) || empty($filters['start']) || empty($filters['end'])) {
            $filters = $statistic->getFilters();
        }

        try {
            $previousStatistics = $statistic->filter(
                $this->getPreviousPeriodFilters($filters)
            )->get();
        } catch (\Throwable $e) {
            return $trend;
        }

        // Compare the sum of both periods
        $statisticSumCurrentPeriod = $currentStatistics->sum('amount');
        $statisticSumPreviousPeriod = $previousStatistics->sum('amount');

        if ($statisticSumCurrentPeriod > $statisticSumPreviousPeriod) {
            $trend = self::TREND_UP;
        }

        if ($statisticSumCurrentPeriod < $statisticSumPreviousPeriod) {
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
        $diffInDays = $start->diffInDays($end);

        // Previous end is one day before current start
        $previousEnd = $start->copy()->subDay();
        $previousStart = $previousEnd->copy()->subDays($diffInDays);

        return [
            'start' => $previousStart->format('Ymd'),
            'end'   => $previousEnd->format('Ymd'),
        ];
    }
}
