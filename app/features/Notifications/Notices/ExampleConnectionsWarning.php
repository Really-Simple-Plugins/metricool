<?php

namespace Metricool\Features\Notifications\Notices;

use Metricool\App;
use Metricool\Helpers\MetricoolUrl;

class ExampleConnectionsWarning extends AbstractNotice
{
    const IDENTIFIER = 'example_connections_warning';
    protected bool $active = true;

    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        return __('This notice is a warning.', 'metricool');
    }

    /**
     * @inheritDoc
     */
    public function getText(): string
    {
        return __('This notice is a warning.', 'metricool');
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
            'text' => __('Connect', 'metricool'),
            'link' => MetricoolUrl::adminUrl(App::env('metricool.connect_network_url')),
            'target' => '_blank',
        ];
    }
}