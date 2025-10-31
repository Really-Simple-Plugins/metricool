<?php

namespace Metricool\Features\Notifications\Notices;

class ExampleInactiveNotice extends AbstractNotice
{
    const IDENTIFIER = 'example_inactive_notice';
    protected bool $active = false;

    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        return esc_html__('This is an inactive notice.', 'metricool');
    }

    /**
     * @inheritDoc
     */
    public function getText(): string
    {
        return esc_html__('This is an inactive notice.', 'metricool');
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
        return 'general';
    }


    /**
     * @inheritDoc
     */
    public function getAction(): array
    {
        return [

        ];
    }
}