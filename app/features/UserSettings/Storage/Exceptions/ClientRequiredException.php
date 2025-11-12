<?php

namespace Metricool\Features\UserSettings\Storage\Exceptions;
class ClientRequiredException extends \RuntimeException
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}