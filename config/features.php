<?php

use Metricool\Helpers\FeatureHelper;

if (!defined('ABSPATH')) {
    exit;
}

return [
    'UserSettings' => [
        'enabled' => true,
        'inScope' => true,
        'pro' => false,
        'priorityFiles' => [
            'Storage/AbstractStorage',
            'Validators/AbstractValidator',
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