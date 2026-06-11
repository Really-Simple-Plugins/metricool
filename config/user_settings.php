<?php

return [
    'fields' => [
        'blogId' => [
            'storage' => 'default',
        ],
        'name' => [
            'section' => 'personal',
            'storage' => 'metricool',
            'validators' => ['required'],
        ],
        'lastName' => [
            'section' => 'personal',
            'storage' => 'metricool',
        ],
        'language' => [
            'section' => 'preferences',
            'storage' => 'metricool',
            'validators' => ['supportedLanguage'],
        ],
        'timezone' => [
            'section' => 'preferences',
            'storage' => 'metricool',
            'validators' => ['timezone'],
        ],
        'firstDayOfTheWeek' => [
            'section' => 'preferences',
            'storage' => 'metricool',
            'validators' => ['firstDayOfWeek'],
        ],
        'sendToAlternativeEmail' => [
            'section' => 'account',
            'validators' => ['required'],
            'type' => 'boolean',
            'storage' => 'metricool',
        ],
        'alternativeEmail' => [
            'section' => 'account',
            'validators' => ['requiredIf:sendToAlternativeEmail,true', 'email'],
            'storage' => 'metricool',
        ],
        'trackingScriptActive' => [
            'section' => 'tracking',
            'type' => 'boolean',
            'defaultValue' => false,
        ],
        'trackingScriptHash' => [
            'section' => 'tracking',
        ]
    ],
    'storages' => [
        'default' => [
            'class' => 'OptionsStorage',
            'prefix' => 'metricool_',
            'casing' => 'snakeCase',
        ],
        'metricool' => [
            'class' => 'RemoteStorage',
            'method' => 'patch',
            'casing' => 'camelCase',
        ],
    ],
];
