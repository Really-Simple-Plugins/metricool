<?php

declare(strict_types=1);

namespace Metricool\Managers;

use Metricool\Interfaces\FeatureInterface;

final class FeatureManager extends AbstractManager
{
    /**
     * @inheritDoc
     */
    protected function type(): string
    {
        return 'features';
    }

    /**
     * @inheritDoc
     */
    public function isRegistrable(object $class): bool
    {
        return $class instanceof FeatureInterface;
    }

    /**
     * @inheritDoc
     */
    public function registerClass(object $class): void
    {
        if ($class->isEnabled() && $class->inScope()) {
            $class->boot();
        }
    }
}
