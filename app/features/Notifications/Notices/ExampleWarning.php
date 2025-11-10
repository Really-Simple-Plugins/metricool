<?php

namespace Metricool\Features\Notifications\Notices;

class ExampleWarning extends AbstractNotice
{
    const IDENTIFIER = 'example_warning';
    protected bool $active = true;

    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        return __('This is a warning without a route.', 'metricool');
    }

    /**
     * @inheritDoc
     */
    public function getText(): string
    {
        return __('This is a warning without a route.', 'metricool');
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
        return '';
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