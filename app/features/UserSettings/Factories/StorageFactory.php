<?php

namespace Metricool\Features\UserSettings\Factories;

use Metricool\Features\UserSettings\Storage\AbstractStorage;

class StorageFactory
{
    private const STORAGE_NAMESPACE = '\\Metricool\\Features\\UserSettings\\Storage\\';

    public static function create(string $name, array $options): AbstractStorage
    {
        $storageClass = self::STORAGE_NAMESPACE . ucfirst($options['type']) . 'Storage';

        if (!class_exists($storageClass)) {
            throw new \InvalidArgumentException('Storage "' . $storageClass . '" not found');
        }

        return new $storageClass($name, $options);
    }
}