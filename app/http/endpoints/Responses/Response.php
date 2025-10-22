<?php

namespace Metricool\Http\Endpoints\Responses;

/**
 * Response class that serves as a blueprint for creating custom Responses for WordPress REST API endpoints.
 */
abstract class Response
{
    /**
     * Creates the response body
     */
    public abstract function body(): array;
}