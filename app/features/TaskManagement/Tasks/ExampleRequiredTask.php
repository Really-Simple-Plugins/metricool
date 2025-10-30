<?php


namespace Metricool\Features\TaskManagement\Tasks;

class ExampleRequiredTask extends AbstractTask
{
    const IDENTIFIER = 'example_required';

    protected bool $required = true;

    /**
     * @inheritDoc
     */
    public function getText(): string
    {
        return esc_html__('This is a premium task', 'metricool');
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