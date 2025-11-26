<?php

declare(strict_types=1);

namespace Metricool\Features\TaskManagement\Tasks;

class ExampleCompletedTask extends AbstractTask
{
    public const IDENTIFIER = 'example_completed';

    protected bool $required = false;
    protected string $status = self::STATUS_COMPLETED;

    /**
     * @inheritDoc
     */
    public function getText(): string
    {
        return __('This is a completed task', 'metricool');
    }

    /**
     * @inheritDoc
     */
    public function getAction(): array
    {
        return [
            'text' => __('Example button', 'metricool'),
            'link' => 'https://example.test',
            'target' => '_blank',
        ];
    }
}
