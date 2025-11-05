<?php

use Metricool\App;

return [
    'fields' => [
        'send_to_alternative_email' => [
            'type' => 'boolean',
            'section' => 'account',
            'storage' => 'metricool',
        ],
        'alternative_email' => [
            'type' => 'email',
            'section' => 'account',
            'storage' => 'metricool',
        ],
        'tracking_script_active' => [
            'type' => 'boolean',
            'default_value' => true,
            'section' => 'tracking',
        ],
        'tracking_script_hash' => [
            'required' => true,
            'type' => 'string',
            'section' => 'tracking',
            'storage' => 'database',
        ],
    ],
    'storages' => [
        'database' => [
            'prefix' => 'metricool_',
        ],
        'metricool' => [
            'type' => 'api',
            'client' => App::provide('client')->userSettings(),
            'method' => 'patch',
            'casing' => 'camel_case',
        ],
    ],
];
