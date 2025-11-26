<?php

declare(strict_types=1);

namespace Metricool\Features\TaskManagement\Tasks;

class ExamplePremiumTask extends AbstractTask
{
    public const IDENTIFIER = 'example_premium';

    protected bool $required = false;
    protected bool $premium = true;

    /**
     * @inheritDoc
     */
    public function getText(): string
    {
        return __('This is a premium task', 'metricool');
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
