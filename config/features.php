<?php

use Metricool\Helpers\FeatureHelper;

if (!defined('ABSPATH')) {
    exit;
}

return [
    'DashboardManagement' => [
        'enabled' => true,
        'inScope' => is_admin(), // todo - add a check like "is on dashboard page"
        'pro' => false,
        'dependencies' => [
            '\Metricool\Features\DashboardManagement\Menu\MenuFacade',
        ],
    ],
    'Onboarding' => [
        'enabled' => FeatureHelper::isEnabled('onboarding'),
        'inScope' => is_admin() || metricool_is_wp_json_request(),
        'pro' => false,
        'dependencies' => [
            'Service',
        ],
    ],
    'TaskManagement' => [
        'enabled' => FeatureHelper::isEnabled('task_management'),
        'inScope' => true, // Should be able to listen everywhere
        'pro' => false,
        'priorityFiles' => [
            'Tasks/AbstractTask',
        ],
    ],
    'Notifications' => [
        'enabled' => FeatureHelper::isEnabled('notifications'),
        'inScope' => true, // Should be able to listen everywhere
        'pro' => false,
        'priorityFiles' => [
            'Notices/AbstractNotice',
        ],
    ],
];