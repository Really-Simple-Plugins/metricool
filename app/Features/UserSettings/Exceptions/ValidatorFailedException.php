<?php

declare(strict_types=1);

namespace Metricool\Features\UserSettings\Exceptions;

use Metricool\Features\UserSettings\Validators\AbstractValidator;

/**
 * This exception is thrown when a validator failed validation
 */
class ValidatorFailedException extends \RuntimeException
{
    public AbstractValidator $validator;

    public function __construct(string $message, AbstractValidator $validator)
    {
        $this->validator = $validator;

        parent::__construct($message);
    }
}
