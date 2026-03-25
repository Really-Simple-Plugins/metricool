<?php

declare(strict_types=1);

namespace Metricool\Support\Helpers;

use Metricool\Traits\DeletesOptions;

class Uninstall
{
    use DeletesOptions;

    /**
     * Handle plugin uninstallation.
     * @internal Method is currently hooked as the uninstallation callback
     * {@see Metricool\Bootstrap\Plugin::boot}
     */
    public function handlePluginUninstall(): void
    {
        $this->deleteAllOptions(true);
    }
}
