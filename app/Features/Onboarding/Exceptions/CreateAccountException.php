<?php

namespace Metricool\Features\Onboarding\Exceptions;

class CreateAccountException extends \RuntimeException
{
    public string $reason;

    public function __construct(string $message, string $reason, int $code = 500)
    {
        $this->reason = $reason;
        parent::__construct($message, $code);
    }
}
