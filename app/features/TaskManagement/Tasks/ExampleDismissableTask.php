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
        return __('This is a dismissable task', 'metricool');
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