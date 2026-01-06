<?php

declare(strict_types=1);

namespace Metricool\Features\TaskManagement\Tasks;

class ExampleUrgentTask extends AbstractTask
{
    public const IDENTIFIER = 'example_urgent';

    protected bool $required = false;
    protected string $status = 'urgent';

    /**
     * @inheritDoc
     */
    public function getText(): string
    {
        return __('This is an urgent task', 'metricool');
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
