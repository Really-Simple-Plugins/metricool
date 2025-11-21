<?php

namespace Metricool\Features\UserSettings\Exceptions;

/**
 * This Exception is thrown when a client is not set in a storage configuration
 */
class ClientRequiredException extends \RuntimeException
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}