<?php

namespace Metricool\Features\UserSettings\Factories;

use Metricool\Features\UserSettings\Storage\AbstractStorage;

class StorageFactory
{
    private const STORAGE_NAMESPACE = '\\Metricool\\Features\\UserSettings\\Storage\\';

    /**
     * Creates a storage from the user_settings configuration
     * @see config/user_settings.php
     */
    public static function createFromConfig(string $name, array $options): AbstractStorage
    {
        $storageClass = self::STORAGE_NAMESPACE . ucfirst($options['type']) . 'Storage';

        if (!class_exists($storageClass)) {
            throw new \InvalidArgumentException('Storage "' . $storageClass . '" not found');
        }

        return new $storageClass($name, $options);
    }
}