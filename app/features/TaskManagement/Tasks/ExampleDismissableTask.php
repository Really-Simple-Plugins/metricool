<?php

namespace Metricool\Features\TaskManagement\Tasks;

class ExampleDismissableTask extends AbstractTask
{
    const IDENTIFIER = 'example_dismissable';

    protected bool $required = false;

    /**
     * @inheritDoc
     */
    public function getText(): string
    {
        return esc_html__('This is a dismissable task', 'metricool');
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