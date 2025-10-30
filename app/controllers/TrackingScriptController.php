<?php

namespace Metricool\Controllers;

use Metricool\App;
use Metricool\Interfaces\ControllerInterface;
use Metricool\Services\TrackingScriptService;
use Metricool\Traits\HasViews;

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
        if ($this->service->canRenderTrackingScript()) {
            add_action('wp_footer', [$this, 'renderTrackingWidget']);
        }
    }

    public function renderTrackingWidget(): void
    {
        $this->render('public/tracking-script', [
            'script' => App::env('metricool.tracking_script'),
            'hash' => $this->service->getTrackingHash(),
        ]);
    }
}