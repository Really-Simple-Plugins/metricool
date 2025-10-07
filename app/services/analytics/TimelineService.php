<?php

namespace Metricool\Services\Analytics;

use Carbon\Carbon;

class TimelineService
{
    /**
     * Combines statistics within the same timestamp data into a timeline.
     * Useful for the dashboard charts.
     */
    public function createTimeLine($statistics): array
    {
        $timeline = [];
        $columns = array_keys($statistics);

        // add all statistics to timeline
        foreach ($statistics as $statistic => $results) {
            foreach ($results as $result) {
                if (!isset($timeline[$result[0]])) {
                    // build a row
                    foreach ($columns as $column) {
                        $timeline[$result[0]][$column] = 0.0;
                    }
                }
                // add data to row, 0 is timestamp, 1 is the value
                $timeline[$result[0]][$statistic] = (float) $result[1];
            }
        }

        // add formatted date to each
        foreach ($timeline as $timestamp => &$line) {
            $line['timestamp'] = $timestamp;
            $line['date'] = Carbon::createFromTimestamp($timestamp / 1000)->format('j M');
        }

        return array_values($timeline);
    }
}
