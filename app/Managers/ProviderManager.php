<?php

declare(strict_types=1);

namespace Metricool\Managers;

use Metricool\Interfaces\ProviderInterface;

final class ProviderManager extends AbstractManager
{
    /**
     * @inheritDoc
     */
    protected function type(): string
    {
        return 'providers';
    }

    /**
     * @inheritDoc
     */
    public function isRegistrable(object $class): bool
    {
        return $class instanceof ProviderInterface;
    }

    /**
     * @inheritDoc
     */
    public function registerClass(object $class): void
    {
        $class->provide();
    }

}
