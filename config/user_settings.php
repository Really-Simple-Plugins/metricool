<?php

return [
    'fields' => [
        'blogId' => [
            'storage' => 'default',
        ],
        'sendToAlternativeEmail' => [
            'validators' => ['required'],
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
        ]
    ],
    'storages' => [
        'default' => [
            'class' => 'OptionsStorage',
            'prefix' => 'metricool_',
            'casing' => 'snakeCase',
        ],
        'metricoolUserSettings' => [
            'class' => 'RemoteStorage',
            'method' => 'patch',
            'casing' => 'camelCase',
        ],
    ],
];
