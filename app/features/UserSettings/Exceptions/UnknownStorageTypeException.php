<?php

namespace Metricool\Features\UserSettings\Exceptions;

class UnknownStorageTypeException extends \RuntimeException
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}