<?php


namespace Metricool\Features\TaskManagement\Tasks;

class ExamplePremiumTask extends AbstractTask
{
    const IDENTIFIER = 'example_premium';

    protected bool $required = false;
    protected bool $premium = true;

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