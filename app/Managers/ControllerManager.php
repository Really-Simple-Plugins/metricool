<?php

declare(strict_types=1);

namespace Metricool\Managers;

use Metricool\Interfaces\ControllerInterface;

final class ControllerManager extends AbstractManager
{
    /**
     * @inheritDoc
     */
    protected function type(): string
    {
        return 'controllers';
    }

    /**
     * @inheritDoc
     */
    public function isRegistrable(object $class): bool
    {
        return $class instanceof ControllerInterface;
    }

    /**
     * @inheritDoc
     */
    public function registerClass(object $class): void
    {
        $class->register();
    }
}
