<?php

namespace Metricool\Features\Onboarding\Exceptions;

use Metricool\Exceptions\AbstractLocalizedException;

/**
 * Exception thrown when the user does not have access to the specific brand
 */
class BrandAccessDeniedException extends AbstractLocalizedException
{
    protected const MESSAGE = 'Blog access denied.';

    public function __construct()
    {
        parent::__construct('', 403);

        $this->setLocalizedMessage(
            __('You don’t have sufficient permissions to connect this brand. Please pick another brand or sign in with a different account.', 'metricool')
        );
    }
}
