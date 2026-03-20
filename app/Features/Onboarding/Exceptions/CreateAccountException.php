<?php

namespace Metricool\Features\Onboarding\Exceptions;

class CreateAccountException extends \RuntimeException
{
    public string $reason;

    public function __construct(string $message, string $reason)
    {
        $this->reason = $reason;
        parent::__construct($message);
    }
}
