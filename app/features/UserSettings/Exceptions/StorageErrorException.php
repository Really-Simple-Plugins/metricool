<?php

namespace Metricool\Features\UserSettings\Exceptions;

class StorageErrorException extends \RuntimeException
{
    public function __construct($message)
    {
        parent::__construct($message);
    }
}