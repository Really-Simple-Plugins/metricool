<?php namespace Metricool\Managers;

use Metricool\Interfaces\ProviderInterface;

final class ProviderManager
{
    /**
     * Register a providers as long as it implements the ProviderInterface
     * @uses do_action metricool_providers_loaded
     */
    public function registerProviders(array $providers)
    {
        // Reject all given providers when they do not implement the ProviderInterface
        $providers = array_filter($providers, function ($provider) {
            return $provider instanceof ProviderInterface;
        });

        // Serve each provider
        foreach ($providers as $provider) {
            $provider->provide();
        }

        do_action('metricool_providers_loaded');
    }
}