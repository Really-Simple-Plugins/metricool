<?php

namespace Metricool\Features\Onboarding\Exceptions;

class TooManyBrandsException extends \RuntimeException
{
    public array $connectedBrands;

    public function __construct(array $connectedBrands)
    {
        $this->connectedBrands = $connectedBrands;

        parent::__construct('Too many brands found.', 400);
    }

    /**
     * Return an error response that can be returned to the user.
     */
    public function getErrors(): array
    {
        return [
            'message' => __('Please select a brand.', 'metricool'),
            'connected_brands' => $this->connectedBrands,
        ];
    }
}