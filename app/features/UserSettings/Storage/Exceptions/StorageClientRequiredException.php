<?php

namespace Metricool\Features\UserSettings\Storage\Exceptions;
class StorageClientRequiredException extends \RuntimeException
{
    public function __construct($message)
    {
        parent::__construct($message);
    }
}