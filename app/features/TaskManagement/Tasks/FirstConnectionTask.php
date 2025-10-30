<?php

namespace Metricool\Features\TaskManagement\Tasks;

use Metricool\App;
use Metricool\Helpers\MetricoolUrl;

class FirstConnectionTask extends AbstractTask
{
    const IDENTIFIER = 'first_connection';

    protected bool $required = false;


    /**
     * @inheritDoc
     */
    public function getText(): string
    {
        return esc_html__('Connect your first social account to Metricool', 'metricool');
    }

    /**
     * @inheritDoc
     */
    public function getAction(): array
    {
        return [
            'text' => esc_html__('Connect', 'metricool'),
            'link' => MetricoolUrl::adminUrl(App::env('metricool.connect_network_url')),
            'target' => '_blank',
        ];
    }
}