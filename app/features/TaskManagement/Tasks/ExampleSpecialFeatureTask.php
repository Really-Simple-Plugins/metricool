<?php


namespace Metricool\Features\TaskManagement\Tasks;

class ExampleSpecialFeatureTask extends AbstractTask
{
    const IDENTIFIER = 'example_special_feature';

    protected bool $specialFeature = true;

    /**
     * @inheritDoc
     */
    public function getText(): string
    {
        return esc_html__('This is a Special Feature task', 'metricool');
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