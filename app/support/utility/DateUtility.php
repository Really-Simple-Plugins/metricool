<?php

namespace Metricool\Utility;

use IntlDateFormatter;

class DateUtility
{
    public static function getLocalIsoDateFormat(): string
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