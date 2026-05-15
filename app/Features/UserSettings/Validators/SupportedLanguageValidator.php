<?php

declare(strict_types=1);

namespace Metricool\Features\UserSettings\Validators;

use Metricool\Features\UserSettings\Exceptions\ValidatorFailedException;
use Metricool\Http\Metricool\MetricoolApi;
use Metricool\Support\Helpers\Collection;

class SupportedLanguageValidator extends AbstractValidator
{
    /**
     * This validator checks if the value is 1
     */
    public function validate($value, \WP_REST_Request $request = null): void
    {
        $availableLanguages = new Collection(MetricoolApi::supportedLanguages());

        if ($availableLanguages->where('value', $value)->count() == 0) {
            // translators: %s is the invalid language code submitted by the user.
            throw new ValidatorFailedException(sprintf(__('%s is not a supported language', 'metricool'), $value), $this);
        }
    }
}
