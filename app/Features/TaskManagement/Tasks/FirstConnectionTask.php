<?php

declare(strict_types=1);

namespace Metricool\Features\TaskManagement\Tasks;

use Metricool\Bootstrap\App;
use Metricool\Support\Helpers\MetricoolUrl;

class FirstConnectionTask extends AbstractTask
{
    public const IDENTIFIER = 'first_connection';

    protected bool $required = false;


    /**
     * @inheritDoc
     */
    public function getText(): string
    {
        return __('Connect your first social account to Metricool', 'metricool');
    }

    /**
     * @inheritDoc
     */
    public function getAction(): array
    {
        return [
            'text' => __('Connect', 'metricool'),
            'link' => MetricoolUrl::adminUrl(App::getInstance()->env->getUrl('metricool.connect_network_url')),
            'target' => '_blank',
        ];
    }
}
