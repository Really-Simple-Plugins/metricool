<?php

namespace Metricool\Http\RSPAL;

use Psr\Http\Message\ResponseInterface;

class RspalApiResponse
{
    public int $statusCode;
    public object $data;

    public function __construct(int $statusCode, ?object $data)
    {
        $this->statusCode = $statusCode;
        $this->data = $data;
    }

    public static function fromResponse(ResponseInterface $response): self
    {
        $statusCode = $response->getStatusCode();
        $body = $response->getBody()->getContents();
        $data = json_decode($body, false);

        return new self(
            $statusCode,
            $data
        );
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getData(): object
    {
        return $this->data;
    }
}
