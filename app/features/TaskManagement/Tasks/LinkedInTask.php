<?php

namespace Metricool\Features\TaskManagement\Tasks;

// todo - add listener
class LinkedInTask extends AbstractTask
{
    const IDENTIFIER = 'connect_linkedin';

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
        return esc_html__('Connect your LinkedIn account', 'metricool');
    }

    /**
     * @inheritDoc
     */
    public function getAction(): array
    {
        return [
            'text' => esc_html__('Connect', 'metricool'),
            'link' => 'https://app.metricool.com/evolution/linkedin',
            'target' => '_blank',
        ];
    }
}