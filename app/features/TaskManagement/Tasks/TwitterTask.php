<?php

namespace Metricool\Features\TaskManagement\Tasks;

// todo - add listener
class TwitterTask extends AbstractTask
{
    const IDENTIFIER = 'connect_twitter';

    /**
     * @inheritDoc
     */
    protected bool $required = true;

    /**
     * @inheritDoc
     */
    protected bool $premium = true;

    /**
     * @inheritDoc
     */
    public function getText(): string
    {
        return esc_html__('Connect your Twitter account', 'metricool');
    }

    /**
     * @inheritDoc
     */
    public function getAction(): array
    {
        return [
            'text' => esc_html__('Connect', 'metricool'),
            'link' => 'https://app.metricool.com/evolution/twitter',
            'target' => '_blank',
        ];
    }
}