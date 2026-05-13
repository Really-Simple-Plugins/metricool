<?php

namespace Metricool\Controllers;

use Metricool\Interfaces\ControllerInterface;
use Metricool\Services\MetricoolUserService;

class MetricoolUserController implements ControllerInterface
{
    private MetricoolUserService $service;

    public function __construct(MetricoolUserService $service)
    {
        $this->service = $service;
    }

    public function register(): void
    {
        // Update the user account when user loads dashboard
        add_action('toplevel_page_metricool', [$this->service, 'updateUserFromApi']);
    }
}