<?php

if (!function_exists('metricool_is_wp_json_request')) {
    /**
    * Check if the current request is a WP JSON request. This is better than
    * the WordPress native function `wp_is_json_request()`, because that
    * returns false when visiting /wp-json/ or ?rest_route= (for plain
    * permalinks) endpoint. We need a rue value there to activate features that
    * register REST routes. For example
    * {@see \Metricool\Features\Onboarding\OnboardingController}
    *
    * @internal Ignore the phpcs errors for this method, as they are false
    * positives. We do not actually use the $_GET or $_SERVER variables
    * directly, but we need to check if they are set and contain the
    * expected values.
    */
    function metricool_is_wp_json_request(): bool {
        $restUrlPrefix = trailingslashit(rest_get_url_prefix());
        // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
        $currentRequestUri = ($_SERVER['REQUEST_URI'] ?? '');
        // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.NonceVerification.Recommended
        $isPlainPermalink = isset($_GET['rest_route']) && strpos($_GET['rest_route'], 'metricool/v') !== false;

        return (strpos($currentRequestUri, $restUrlPrefix) !== false) || $isPlainPermalink;
    }
}