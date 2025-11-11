<?php

namespace Metricool\Features\UserSettings\Exceptions;

class StorageNotFoundException extends \RuntimeException
{
    public function __construct($message)
    {
        parent::__construct($message);
    }
}