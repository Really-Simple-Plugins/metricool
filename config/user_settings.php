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
            'validators' => ['requiredIf:sendToAlternativeEmail,true', 'email'],
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
        'exampleRequiredInteger' => [
            'type' => 'integer',
            'validators' => ['required'],
            'section' => 'example',
        ],
        'exampleField' => [
            'field' => 'ExampleField',
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
