<?php

declare(strict_types=1);

namespace Metricool\Support\Helpers;

use Metricool\Services\OptionsService;
use Metricool\Traits\DeletesOptions;

class Uninstall
{
    use DeletesOptions;

    private OptionsService $options;

    public function __construct(OptionsService $options)
    {
        $this->options = $options;
    }

    /**
     * Handle plugin uninstallation.
     * @internal Method is currently hooked as the uninstallation callback
     * {@see Metricool\Bootstrap\Plugin::boot}
     */
    public function handlePluginUninstall(): void
    {
        $this->options->wipe(true);
    }
}
