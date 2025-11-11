<?php

use Metricool\App;
use Metricool\Features\UserSettings\Fields\Exceptions\ValidationFailedException;

return [
    'fields' => [
        'sendToAlternativeEmail' => [
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
        'validateExample' => [
            'defaultValue' => 2,
            'type' => 'integer',
            'validate' => function ($value) {
                if ($value !== 1) {
                    throw new ValidationFailedException(__('Value must be a 1'));
                }
            }
        ]
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
