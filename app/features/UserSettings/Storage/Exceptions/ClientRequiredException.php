<?php

namespace Metricool\Features\UserSettings\Storage\Exceptions;
class ClientRequiredException extends \RuntimeException
{
    public function __construct($message)
    {
        parent::__construct($message);
    }
}