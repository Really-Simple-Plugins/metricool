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
        return __('This is a required task', 'metricool');
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