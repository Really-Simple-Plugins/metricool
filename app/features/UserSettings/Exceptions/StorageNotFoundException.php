<?php

namespace Metricool\Features\UserSettings\Exceptions;

class StorageNotFoundException extends \RuntimeException
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}