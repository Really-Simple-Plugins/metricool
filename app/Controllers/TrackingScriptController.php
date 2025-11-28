<?php

declare(strict_types=1);

namespace Metricool\Controllers;

use Metricool\Bootstrap\App;
use Metricool\Traits\HasViews;
use Metricool\Services\TrackingScriptService;
use Metricool\Interfaces\ControllerInterface;

class TrackingScriptController implements ControllerInterface
{
    use hasViews;

    private TrackingScriptService $service;

    public function __construct(TrackingScriptService $service)
    {
        $this->service = $service;
    }

    public function register(): void
    {
        add_action('wp_footer', [$this, 'renderTrackingWidget']);
    }

    public function renderTrackingWidget(): void
    {
        if (!$this->canRenderTrackingScript()) {
            return;
        }

        $this->render('public/tracking-script', [
            'script_url' => App::getInstance()->env->getUrl('metricool.tracking_script_url'),
            'hash' => $this->service->getTrackingHash(),
        ]);
    }

    /**
     * Checks if the tracking script can be rendered. Only if the tracking hash
     * is set and the user has enabled the widget.
     */
    private function canRenderTrackingScript(): bool
    {
        $trackingHashIsNotEmpty = (strlen($this->service->getTrackingHash()) > 0);
        return $trackingHashIsNotEmpty && $this->service->isTrackingWidgetActive();
    }
}
