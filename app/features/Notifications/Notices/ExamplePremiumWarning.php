<?php

namespace Metricool\Features\Notifications\Notices;

class ExamplePremiumWarning extends AbstractNotice
{
    const IDENTIFIER = 'example_premium_warning';
    protected bool $active = true;
    protected bool $premium = true;

    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        return esc_html__('This is an premium warning.', 'metricool');
    }

    /**
     * @inheritDoc
     */
    public function getText(): string
    {
        return esc_html__('This is an premium warning.', 'metricool');
    }

    /**
     * @inheritDoc
     */
    public function getType(): string
    {
        return self::TYPE_WARNING;
    }

    /**
     * @inheritDoc
     */
    public function getRoute(): string
    {
        return 'general';
    }


    /**
     * @inheritDoc
     */
    public function getAction(): array
    {
        return [
            'text' => esc_html__('Example text', 'metricool'),
            'link' => 'https://example.test',
            'target' => '_blank',
        ];
    }
}