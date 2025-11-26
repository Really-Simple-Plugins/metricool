<?php

declare(strict_types=1);

namespace Metricool\Support\Utility;

use IntlDateFormatter;

class DateUtility
{
    /**
     * Use this function to retrieve if the month comes before the day in the
     * user's local date format.
     */
    public static function localIsoDateMonthFormat(): string
    {
        $formatter = new IntlDateFormatter(
            get_locale(),
            IntlDateFormatter::SHORT,
            IntlDateFormatter::NONE
        );

        // Get the pattern
        $pattern = $formatter->getPattern();

        // Check if month comes before day
        $monthPos = strpos($pattern, 'M');
        $dayPos = strpos($pattern, 'd');

        return $monthPos < $dayPos ? 'MM/DD' : 'DD/MM';
    }
}
