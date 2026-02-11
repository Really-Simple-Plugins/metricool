<?php

namespace Metricool\Features\Onboarding\Exceptions;

class BrandAccessDeniedException extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct('Blog access denied.', 403);
    }
}