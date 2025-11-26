<?php

declare(strict_types=1);

namespace Metricool\Http\Metricool\Traits;

/**
 * Parent classes should define an endpoint property
 * @property string $endpoint
 */
trait IsUpdatable
{
    /**
     * This method is used to retrieve the fillable fields for the entity. Using
     * a method to accept parent classes to update the {@see fillable} property
     * after the class is instantiated.
     */
    abstract protected function getFillable(): array;

    /**
     * This is the main method that should be used to update the entity. Under
     * the hood it will clean the data array to only include the fields that
     * are defined as fillable in the parent class. With that data a PATCH
     * request will be made to the endpoint defined in the parent class.
     */
    public function update(array $data = [])
    {
        if (empty($this->endpoint)) {
            throw new \InvalidArgumentException('Endpoint cannot be empty');
        }

        $data = $this->cleanUpdateData($data);

        $response = $this->client->patch(
            $this->getUpdateEndpoint($data),
            json_encode($data)
        );
        return ($response['data'] ?? []);
    }

    /**
     * Method will remove all the key-value pairs from the data array that
     * are not defined as fillable in the parent class.
     */
    private function cleanUpdateData(array $data): array
    {
        return array_intersect_key($data, array_flip($this->getFillable()));
    }

    /**
     * This method will return the endpoint to be used for the update request.
     * It will append the fields as query parameters to the endpoint.
     *
     * @example /v2/settings/users/123?fields=name&fields=language
     *
     * @internal We cannot use {@see add_query_arg()} here because it does not
     * support multiple query parameters with the same name:
     * {@see https://core.trac.wordpress.org/ticket/51552}
     */
    private function getUpdateEndpoint(array $updateData): string
    {
        if (empty($this->endpoint)) {
            throw new \InvalidArgumentException('Endpoint cannot be empty');
        }

        $filters = array_keys($updateData);
        $queryString = implode('&', array_map(function ($value) {
            return 'fields=' . urlencode($value);
        }, $filters));

        if (empty($queryString)) {
            return $this->endpoint;
        }

        return $this->endpoint . '?' . $queryString;
    }
}
