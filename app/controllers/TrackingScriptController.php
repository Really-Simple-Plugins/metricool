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
        add_action('wp_footer', [$this, 'renderTrackingWidget']);
    }

    public function renderTrackingWidget(): void
    {
        if (!$this->service->canRenderTrackingScript()) {
            return;
        }
        $this->render('public/tracking-script', [
            'script_url' => App::env('metricool.tracking_script_url'),
            'hash' => $this->service->getTrackingHash(),
        ]);
    }
}