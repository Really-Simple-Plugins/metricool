<?php

namespace Metricool\Http\Metricool\Traits;

use Metricool\Utility\StringUtility;

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

        foreach ($filters as $filterName => $filterValue) {
            if (empty($acceptedFilters[$filterName])) {
                continue;
            }

            if ($this->isFilterValid($filterValue, $acceptedFilters[$filterName]) === false) {
                continue;
            }

            if ($this->applyFilter($filterName, $filterValue)) {
                $this->filtered = true;
            }
        }

        return $this;
    }

    /**
     * Process the filter value based on the pregMatch condition.
     */
    private function isFilterValid(string $filterValue, string $pregMatch): bool
    {
        return (bool)preg_match($pregMatch, $filterValue);
    }

    /**
     * Method used to retrieve the filters used by the parent entity.
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * Method used to apply a filter. Calls apply{FilterName}Filter() method when
     * present.
     */
    private function applyFilter(string $filterName, string $filterValue): bool
    {
        $filterMethod = 'apply' . StringUtility::snakeToPascalCase($filterName) . 'Filter';

        if (method_exists($this, $filterMethod)) {
            return $this->{$filterMethod}($filterValue);
        }

        $this->endpoint = add_query_arg(
            sanitize_text_field($filterName),
            sanitize_text_field($filterValue),
            $this->endpoint
        );

        return true;
    }
}