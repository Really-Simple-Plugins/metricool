<?php

declare(strict_types=1);

namespace Metricool\Features\TaskManagement\Tasks;

use Metricool\Bootstrap\App;
use Metricool\Support\Helpers\MetricoolUrl;

class HistoricalDataTask extends AbstractTask
{
    public const IDENTIFIER = 'store_historical_data';

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
        return __('Gain access to analytics with unlimited historical data.', 'metricool');
    }

    /**
     * @inheritDoc
     */
    public function getAction(): array
    {
        return [
            'text' => __('Upgrade', 'metricool'),
            'link' => MetricoolUrl::adminUrl(App::getInstance()->env->getUrl('metricool.upgrade_premium_url')),
            'target' => '_blank',
        ];
    }
}
