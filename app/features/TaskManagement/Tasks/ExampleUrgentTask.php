<?php


namespace Metricool\Features\TaskManagement\Tasks;

class ExampleUrgentTask extends AbstractTask
{
    const IDENTIFIER = 'example_urgent';

    protected bool $required = false;
    protected string $status = 'urgent';

    /**
     * @inheritDoc
     */
    public function getText(): string
    {
        return esc_html__('This is an urgent task', 'metricool');
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