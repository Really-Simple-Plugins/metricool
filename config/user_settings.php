<?php

use Metricool\App;

return [
    'fields' => [
        'sendToAlternativeEmail' => [
            'type' => 'boolean',
            'section' => 'account',
            'storage' => 'metricool',
        ],
        'alternativeEmail' => [
            'type' => 'email',
            'section' => 'account',
            'storage' => 'metricool',
        ],
        'trackingScriptActive' => [
            'type' => 'boolean',
            'default_value' => true,
            'section' => 'tracking',
        ],
        'trackingScriptHash' => [
            'required' => true,
            'type' => 'string',
            'section' => 'tracking',
            'storage' => 'database',
        ],
    ],
    'storages' => [
        'database' => [
            'type' => 'database',
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
