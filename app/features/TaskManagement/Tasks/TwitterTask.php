<?php

namespace Metricool\Features\TaskManagement\Tasks;

use Metricool\App;
use Metricool\Helpers\MetricoolUrl;

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
            'link' => MetricoolUrl::adminUrl(App::env('metricool.connect_twitter_url')),
            'target' => '_blank',
        ];
    }
}