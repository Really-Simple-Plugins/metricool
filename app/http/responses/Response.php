<?php

namespace Metricool\Http\Responses;

/**
 * Response class that serves as a blueprint for creating custom responses for WordPress REST API endpoints.
 */
abstract class Response
{
    /**
     * Creates the response body
     */
    public abstract function body(): array;
}