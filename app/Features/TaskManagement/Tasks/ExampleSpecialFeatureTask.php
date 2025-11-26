<?php

declare(strict_types=1);

namespace Metricool\Features\TaskManagement\Tasks;

class ExampleSpecialFeatureTask extends AbstractTask
{
    public const IDENTIFIER = 'example_special_feature';

    protected bool $specialFeature = true;

    /**
     * @inheritDoc
     */
    public function getText(): string
    {
        return __('This is a Special Feature task', 'metricool');
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
