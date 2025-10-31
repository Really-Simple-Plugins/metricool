<?php

namespace Metricool\Features\Notifications\Notices;

class ExampleNotice extends AbstractNotice
{
    const IDENTIFIER = 'example_notice';
    protected bool $active = true;

    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        return esc_html__('This is a notice without a route.', 'metricool');
    }

    /**
     * @inheritDoc
     */
    public function getText(): string
    {
        return esc_html__('This is a notice without a route.', 'metricool');
    }

    /**
     * @inheritDoc
     */
    public function getType(): string
    {
        return self::TYPE_INFO;
    }

    /**
     * @inheritDoc
     */
    public function getRoute(): string
    {
        return '';
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