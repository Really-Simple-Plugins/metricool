<?php namespace Metricool\Traits;

use Metricool\App;

trait HasNonces
{
    /**
     * Method for verifying the nonce
     * @param mixed $nonce Preferably string, not type-casted to prevent errors
     */
    protected function verifyNonce($nonce, string $action = 'metricool_nonce'): bool
    {
        if (is_string($nonce) === false) {
            return false;
        }

        return wp_verify_nonce(sanitize_text_field(wp_unslash($nonce)), 'metricool_nonce');
    }
}