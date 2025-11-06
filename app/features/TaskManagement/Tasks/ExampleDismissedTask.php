<?php


namespace Metricool\Features\TaskManagement\Tasks;

class ExampleDismissedTask extends AbstractTask
{
    const IDENTIFIER = 'example_dismissed';

    protected bool $required = false;
    public string $status = 'dismissed';

    /**
     * @inheritDoc
     */
    public function getText(): string
    {
        return __('This is a dismissed task', 'metricool');
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