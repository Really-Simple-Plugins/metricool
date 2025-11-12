<?php

use Metricool\App;

return [
    'fields' => [
        'sendToAlternativeEmail' => [
            'required' => true,
            'type' => 'boolean',
            'section' => 'account',
            'storage' => 'metricoolUserSettings',
        ],
        'alternativeEmail' => [
            'type' => 'email',
            'section' => 'account',
            'storage' => 'metricoolUserSettings',
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
