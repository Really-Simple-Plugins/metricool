<?php

namespace Metricool\Controllers;

use Metricool\Interfaces\ControllerInterface;
use Metricool\Services\TrackingScriptService;
use Metricool\Traits\HasViews;

/**
 * Checks if the tracking script can be loaded and add it to wp_footer
 * Tracking script only loads when 'metricool_tracking_script_hash' includes a
 * hash and 'metricool_tracking_script_active' is true
 */
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
        if ($this->service->trackingScriptActive()) {
            add_action('wp_footer', function () {
                $this->renderTrackingWidget();
            });
        }
    }

    public function renderTrackingWidget(): void
    {
        $this->render('/tracking-script', ['hash' => $this->service->getTrackingHash()]);
    }
}