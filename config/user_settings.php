<?php

use Metricool\App;

return [
    'fields' => [
        'sendToAlternativeEmail' => [
            'type' => 'boolean',
            'storage' => 'metricoolUserSettings',
            'section' => 'account',
        ],
        'alternativeEmail' => [
            'type' => 'string',
            'validator' => 'email',
            'storage' => 'metricoolUserSettings',
            'section' => 'account',
        ],
        'trackingScriptActive' => [
            'type' => 'boolean',
            'defaultValue' => false,
            'section' => 'tracking',
        ],
        'trackingScriptHash' => [
            'section' => 'tracking',
        ],
        'exampleField' => [
            'type' => 'integer',
            'field' => 'example',
            'validators' => ['required', 'example'],
            'section' => 'example',
        ],
    ],
    'storages' => [
        'default' => [
            'type' => 'options',
            'prefix' => 'metricool_',
        ],
        'metricoolUserSettings' => [
            'type' => 'custom',
            'client' => App::provide('client')->userSettings(),
            'method' => 'patch',
            'casing' => 'camelCase',
        ],
    ],
];
