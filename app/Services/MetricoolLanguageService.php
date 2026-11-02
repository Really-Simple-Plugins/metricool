<?php

declare(strict_types=1);

namespace Metricool\Services;

use Metricool\Support\Helpers\Collection;
use Metricool\Support\Helpers\Storages\LanguagesConfig;

class MetricoolLanguageService
{
    private Collection $languages;
    private string $fallbackLanguage = 'en';

    public function __construct(LanguagesConfig $languages)
    {
        $this->languages = new Collection($languages->all());
    }

    /**
     * Get the user's language based on the WordPress locale.
     * The locale is in the format of language_REGION. The region code is checked
     * first, since it can be a supported language itself. If the region code is
     * not accepted by the API, it will use the language code. If neither is
     * accepted by the API, the fallback language is returned.
     *
     * Examples:
     *  - nl_NL -> nl
     *  - pt_BR -> br (BR is a supported language)
     *  - de_AT -> de (AT is not a supported language)
     */
    public function getUserLanguage(): string
    {
        $locale = strtolower(get_user_locale());
        $parts = explode('_', $locale);
        $language = $parts[0];
        $region = $parts[1] ?? null;

        if ($region !== null && $this->isAcceptedLanguage($region)) {
            return $region;
        }

        if (!$this->isAcceptedLanguage($language)) {
            return $this->fallbackLanguage;
        }

        return $language;
    }

    /**
     * Check if the given language is accepted by the API.
     */
    private function isAcceptedLanguage(string $language): bool
    {
        return $this->languages->where('value', $language)->count() > 0;
    }
}
