<?php

namespace Metricool\Features\TaskManagement\Tasks;

use Metricool\App;
use Metricool\Helpers\MetricoolUrl;

class HistoricalDataTask extends AbstractTask
{
    const IDENTIFIER = 'store_historical_data';

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
        return esc_html__('Store historical analytics for 90 days with Premium', 'metricool');
    }

    /**
     * @inheritDoc
     */
    public function getAction(): array
    {
        return [
            'text' => esc_html__('Upgrade', 'metricool'),
            'link' => MetricoolUrl::adminUrl(App::env('metricool.upgrade_premium_url')),
            'target' => '_blank',
        ];
    }
}