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
            // or: 'field' => ExampleField::class, which one do we prefer?
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
        'exampleArray' => [
            'type' => 'array',
            'defaultValue' => ['foo', 'bar'],
            'section' => 'example',
        ],
        'exampleObject' => [
            'type' => 'object',
            'defaultValue' => (object) ['foo' => 'bar'],
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
