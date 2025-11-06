<?php

namespace Metricool\Features\Notifications\Notices;

use Metricool\App;
use Metricool\Helpers\MetricoolUrl;

class FirstConnectionNotice extends AbstractNotice
{
    const IDENTIFIER = 'first_connection';
    protected bool $active = true;

    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        return __('No connections detected', 'metricool');
    }

    /**
     * @inheritDoc
     */
    public function getText(): string
    {
        return __('Connect your first social account to Metricool to start scheduling and tracking your content.', 'metricool');
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
        return 'connections';
    }

    /**
     * @inheritDoc
     */
    public function getAction(): array
    {
        return [
            'text' => __('Connect', 'metricool'),
            'link' => MetricoolUrl::adminUrl(App::env('metricool.connect_network_url')),
            'target' => '_blank',
        ];
    }
}