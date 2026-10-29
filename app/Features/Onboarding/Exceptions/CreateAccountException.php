<?php

namespace Metricool\Features\Onboarding\Exceptions;

use Metricool\Exceptions\AbstractLocalizedException;

class CreateAccountException extends AbstractLocalizedException
{
    public ?string $reason;

    protected const MESSAGE = 'Account creation failed';

    public function __construct(?string $reason = null, int $code = 500)
    {
        parent::__construct('', $code);

        $this->reason = $reason;
    }

    public function getReason(): ?string
    {
        return $this->reason;
    }
}
