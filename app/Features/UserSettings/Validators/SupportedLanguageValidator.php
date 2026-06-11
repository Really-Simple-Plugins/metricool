<?php

declare(strict_types=1);

namespace Metricool\Features\UserSettings\Validators;

use WP_REST_Request;
use Metricool\Support\Helpers\Collection;
use Metricool\Http\Metricool\MetricoolApi;
use Metricool\Features\UserSettings\Exceptions\ValidatorFailedException;

class SupportedLanguageValidator extends AbstractValidator
{
    /**
     * This validator checks if the value is 1
     */
    public function validate($value, ?WP_REST_Request $request = null): void
    {
        $availableLanguages = new Collection(MetricoolApi::supportedLanguages());

        if ($availableLanguages->where('value', $value)->count() == 0) {
            throw new ValidatorFailedException(esc_html(
                sprintf(
                    // translators: %s is the invalid language code submitted by the user.
                    __('%s is not a supported language', 'metricool'),
                    $value
                )
            ));
        }
    }
}
