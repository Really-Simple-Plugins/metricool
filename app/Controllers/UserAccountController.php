<?php

namespace Metricool\Controllers;

use Metricool\Services\UserAccountService;

class UserAccountController
{
    private UserAccountService $service;

    public function __construct(UserAccountService $service)
    {
        $this->service = $service;
    }

    public function register(): void
    {
        // Update the user account when user loads dashboard
        add_action('toplevel_page_metricool', [$this->service, 'updateUserFromApi']);
    }
}