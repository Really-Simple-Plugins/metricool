<?php

declare(strict_types=1);

namespace Metricool\Features\UserSettings;

use Metricool\Features\UserSettings\Endpoints\UserSettingsEndpoints;
use Metricool\Interfaces\FeatureInterface;
use Metricool\Traits\HasAllowlistControl;

final class UserSettingsFeature implements FeatureInterface
{
    use HasAllowlistControl;

    /**
     * @inheritDoc
     */
    public function boot(): void
    {
        add_filter('metricool_plugin_endpoints', [$this, 'registerEndpoints']);
    }

    /**
     * Add the user settings endpoint to the plugin endpoint registry.
     */
    public function registerEndpoints(array $existingEndpoints): array
    {
        return array_merge($existingEndpoints, [UserSettingsEndpoints::class]);
    }

    /**
     * @inheritDoc
     */
    public function isEnabled(): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function inScope(): bool
    {
        return $this->userCanManage();
    }
}
