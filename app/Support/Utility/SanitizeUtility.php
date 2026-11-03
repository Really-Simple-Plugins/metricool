<?php

declare(strict_types=1);

namespace Metricool\Support\Utility;

use Metricool\Bootstrap\App;
use Metricool\Support\Helpers\Storages\EnvironmentConfig;

class SanitizeUtility
{
    /**
     * Use this method to sanitize messages that are displayed to the user. The
     * messages are stripped from all HTML except for <strong> tags and
     * <a href|target|rel> tags that are listed in frontend.trusted_urls.
     */
    public static function sanitizeMessage(string $message): string
    {
        $allowedHtml = [
            'strong' => [],
        ];

        if (!self::hasOnlyTrustedUrls($message)) {
            return wp_kses($message, $allowedHtml);
        }

        $allowForUrls = array_merge([
            'a' => [
                'href'   => true,
                'target' => true,
                'rel'    => true,
            ],
        ], $allowedHtml);

        return wp_kses($message, $allowForUrls, ['http', 'https']);
    }

    /**
     * Check for urls in the message and return true if all urls are trusted.
     */
    private static function hasOnlyTrustedUrls(string $message): bool
    {
        $urls = wp_extract_urls($message);
        $untrusted = array_diff($urls, self::trustedUrls());

        return empty($untrusted);
    }

    /**
     * Returns the trusted URLs from the env config.
     */
    private static function trustedUrls(): array
    {
        $env = App::getInstance()->get(EnvironmentConfig::class);
        return array_values((array) $env->get('frontend.trusted_urls'));
    }
}
