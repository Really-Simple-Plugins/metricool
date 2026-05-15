<?php

declare(strict_types=1);

namespace Metricool\Features\UserSettings\Validators;

use Metricool\Features\UserSettings\Exceptions\ValidatorFailedException;

class RequiredValidator extends AbstractValidator
{
    /**
     * Checks if the field is required and is not empty
     * @inheritDoc
     */
    public function validate($value, ?\WP_REST_Request $request = null): void
    {
        if ($this->isEmptyValue($value)) {
            // phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped -- $this is the validator instance, not output.
            throw new ValidatorFailedException(esc_html__('This field is required', 'metricool'), $this);
        }
    }
}
