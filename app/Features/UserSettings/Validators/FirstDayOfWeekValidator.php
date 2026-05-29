<?php

declare(strict_types=1);

namespace Metricool\Features\UserSettings\Validators;

use Metricool\Features\UserSettings\Exceptions\ValidatorFailedException;
use WP_REST_Request;

class FirstDayOfWeekValidator extends AbstractValidator
{
    /**
     * This validator checks if the value is 1
     */
    public function validate($value, ?WP_REST_Request $request = null): void
    {
        if ($value !== '1' && $value !== '7') {
            throw new ValidatorFailedException(esc_html__('Please enter a valid day of the week (1 or 7)', 'metricool'));
        }
    }
}
