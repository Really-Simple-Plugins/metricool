<?php

declare(strict_types=1);

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
        if (!isset($options['class'])) {
            throw new \InvalidArgumentException('Class for "' . $name . '" not mapped, please add it to the config');
        }

        $storageClass = self::STORAGE_NAMESPACE . ucfirst($options['class']);

        if (!class_exists($storageClass)) {
            throw new \InvalidArgumentException('Storage "' . $storageClass . '" not found');
        }

        return new $storageClass($name, $options);
    }
}
