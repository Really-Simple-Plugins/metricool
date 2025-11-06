<?php


namespace Metricool\Features\TaskManagement\Tasks;

class ExampleCompletedTask extends AbstractTask
{
    const IDENTIFIER = 'example_completed';

    protected bool $required = false;
    public string $status = 'completed';

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