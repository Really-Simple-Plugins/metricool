<?php

namespace Metricool\Features\UserSettings\Exceptions;

class StorageFailedException extends \RuntimeException
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}