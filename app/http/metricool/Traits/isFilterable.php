<?php

namespace Metricool\Http\Metricool\Traits;

/**
 * Parent classes should define an endpoint property
 * @property string $endpoint
 */
trait isFilterable
{
    /**
     * The filters to be applied to the endpoint.
     */
    protected array $filters = [];

    /**
     * Indicates if the endpoint has been filtered.
     */
    protected bool $filtered = false;

    /**
     * Indicates if the endpoint requires filters to be applied. If set to
     * false, the endpoint can be used without filters.
     */
    protected bool $requiresFilter = false;

    /**
     * Define the accepted filters for this entity. The keys are the filter
     * names and the values are regex patterns to validate them.
     * @example [
     *      'filter_name' => '/^regex_pattern$/',
     *      'another_filter' => '/^\d{4}-\d{2}-\d{2}$/',
     * ]
     */
    abstract protected function getAcceptedFilters(): array;

    /**
     * This method is used to add the given filters to the endpoint property.
     * Only filters that are defined in the {@see getAcceptedFilters} method
     * will be added when the value matches the regex pattern.
     *
     * @internal When the parent class has no endpoint property, it will return
     * the current instance without modifying it.
     */
    public function filter(array $filters): self
    {
        if (empty($this->endpoint)) {
            return $this;
        }

        $acceptedFilters = $this->getAcceptedFilters();

        foreach ($filters as $name => $filter) {
            if (empty($acceptedFilters[$name])) {
                continue;
            }

            if ($this->isFilterValid($filter, $acceptedFilters[$name]) === false) {
                continue;
            }

            $this->endpoint = add_query_arg(
                sanitize_text_field($name),
                sanitize_text_field($filter),
                $this->endpoint
            );

            $this->filtered = true;
        }

        return $this;
    }

    /**
     * Process the filter value based on the pregMatch condition.
     */
    private function isFilterValid(string $filter, string $pregMatch): bool
    {
        return preg_match($pregMatch, $filter) ? $filter : false;
    }
}