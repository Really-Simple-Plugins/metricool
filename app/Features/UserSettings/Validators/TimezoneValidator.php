<?php

declare(strict_types=1);

namespace Metricool\Features\UserSettings\Validators;

use Metricool\Features\UserSettings\Exceptions\ValidatorFailedException;

class TimezoneValidator extends AbstractValidator
{
    /**
     * This validator checks if the value is 1
     */
    public function validate($value, \WP_REST_Request $request = null): void
    {
        $validTimezones = timezone_identifiers_list();

        if (!in_array($value, $validTimezones)) {
            // translators: %s is the invalid timezone submitted by the user.
            // phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped -- $this is the validator instance, not output.
            throw new ValidatorFailedException(esc_html(sprintf(__('%s is not a valid timezone', 'metricool'), $value)), $this);
        }
    }
}
