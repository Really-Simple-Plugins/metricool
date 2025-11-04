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
        return esc_html__('This is a completed task', 'metricool');
    }

    /**
     * @inheritDoc
     */
    public function getAction(): array
    {
        return [
            'text' => esc_html__('Example button', 'metricool'),
            'link' => 'https://example.test',
            'target' => '_blank',
        ];
    }
}