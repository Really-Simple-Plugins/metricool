<?php

declare(strict_types=1);

namespace Metricool\Features\UserSettings\Exceptions;

use Metricool\Features\UserSettings\Validators\AbstractValidator;

/**
 * This exception is thrown when a validator failed validation
 */
class ValidatorFailedException extends \RuntimeException
{
}
