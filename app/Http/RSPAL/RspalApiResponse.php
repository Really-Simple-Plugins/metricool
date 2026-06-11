<?php

namespace Metricool\Http\RSPAL;

use Psr\Http\Message\ResponseInterface;

class RspalApiResponse
{
    public int $statusCode;
    public object $data;

    public function __construct(int $statusCode, object $data)
    {
        $this->statusCode = $statusCode;
        $this->data = $data;
    }

    public static function fromResponse(ResponseInterface $response): self
    {
        $statusCode = $response->getStatusCode();
        $body = $response->getBody()->getContents();
        $data = json_decode($body, false);

        if (!is_object($data)) {
            throw new \RuntimeException('Invalid or empty response from the API');
        }

        if ($data->status && $data->status == 'OK') {
            throw new \RuntimeException('Al Error Occurred');
        }

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
