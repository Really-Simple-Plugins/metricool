<?php

return [
    'fields' => [
        'userBlogId' => [
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
        'exampleCustomStorageName' => [
            'validators' => ['email'],
            'settingName' => 'very_custom_storage_name',
            'section' => 'example',
        ],
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
