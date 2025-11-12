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
        // Example fields
        'exampleCustomField' => [
            'field' => 'ExampleField',
            'section' => 'example',
        ],
        'exampleRequiredInteger' => [
            'type' => 'integer',
            'validators' => ['required'],
            'section' => 'example',
        ],
        'exampleFloat' => [
            'type' => 'float',
            'section' => 'example',
        ],
        'exampleString' => [
            'type' => 'string',
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
