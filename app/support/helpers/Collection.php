<?php

namespace Metricool\Helpers;

use ArrayIterator;
use Closure;
use IteratorAggregate;

class Collection implements IteratorAggregate
{
    /**
     * The items contained in the collection.
     */
    protected array $items = [];

    /**
     * Create a new collection.
     */
    public function __construct($items = [])
    {
        $this->items = $items;
    }

    /**
     * Get all the items in the collection.
     */
    public function all(): array
    {
        return $this->items;
    }

    /**
     * Get first item from the collection
     * @return mixed
     */
    public function first()
    {
        return reset($this->items) ?: null;
    }

    /**
     * Push one or more items to the end of the collection
     */
    public function push(...$values): self
    {
        foreach ($values as $value) {
            $this->items[] = $value;
        }

        return $this;
    }

    /**
     * Sort the collection using the given callback.
     */
    public function sortBy($callback, $descending = false, $preserveKeys = false, $options = SORT_REGULAR): self
    {
        $results = [];

        $callback = $this->valueRetriever($callback);

        // First we will loop through the items and get the comparator from a callback
        // function which we were given. Then, we will sort the returned values and
        // grab all the corresponding values for the sorted keys from this array.
        foreach ($this->items as $key => $value) {
            $results[$key] = $callback($value, $key);
        }

        $descending ? arsort($results, $options)
            : asort($results, $options);

        // Once we have sorted all of the keys in the array, we will loop through them
        // and grab the corresponding model so we can set the underlying items list
        // to the sorted version. Then we'll just return the collection instance.
        foreach (array_keys($results) as $key) {
            $results[$key] = $this->items[$key];
        }

        if (!$preserveKeys) {
            $results = array_values($results);
        }

        return new static($results);
    }

    /**
     * Sort the collection in ascending order.
     * @see Collection::sortBy()
     */
    public function sortByAsc($callback, $preserveKeys = false, $options = SORT_REGULAR): self
    {
        return $this->sortBy($callback, $preserveKeys, $options);
    }

    /**
     * Sort the collection in descending order.
     * @see Collection::sortBy()
     */
    public function sortByDesc($callback, $preserveKeys = false, $options = SORT_REGULAR): self
    {
        return $this->sortBy($callback, $preserveKeys, true, $options);
    }

    /**
     * Run a filter over each of the items.
     * @param callable|null $callback
     */
    public function filter(callable $callback = null): self
    {
        if ($callback) {
            return new static(array_filter($this->items, $callback, ARRAY_FILTER_USE_BOTH));
        }

        return new static(array_filter($this->items));
    }

    /**
     * Return all keys of the collection items.
     */
    public function keys(): array
    {
        return array_keys($this->items);
    }

    /**
     * Get the sum of the given values.
     * @param callable|string|null $callback
     * @return float|int
     */
    public function sum($callback = null)
    {
        $callback = is_null($callback)
            ? $this->closure()
            : $this->valueRetriever($callback);

        return $this->reduce(function ($result, $item) use ($callback) {
            return $result + $callback($item);
        }, 0);
    }

    /**
     * Reduce the collection to a single value.
     * @param mixed $initial
     * @return mixed
     */
    public function reduce(callable $callback, $initial = null)
    {
        $result = $initial;

        foreach ($this as $key => $value) {
            $result = $callback($result, $value, $key);
        }

        return $result;
    }

    /**
     * Filter items by the given key value pair. Allows shorthands for:
     * $collection->where('property', 'value'); for a loose comparison check
     * and
     * $collection->where('property'); for a loose boolean check
     * @param mixed $operator
     * @param mixed $value
     */
    public function where(string $key, $operator = null, $value = null): self
    {
        return $this->filter($this->operatorForWhere(...func_get_args()));
    }

    /**
     * Run a map over each of the items.
     */
    public function map(callable $callback): self
    {
        $keys = array_keys($this->items);

        $items = array_map($callback, $this->items, $keys);

        return new static(array_combine($keys, $items));
    }

    /**
     * Count the number of items in the collection.
     */
    public function count(): int
    {
        return count($this->items);
    }

    /**
     * Get the collection of items as a plain array.
     */
    public function toArray(): array
    {
        return $this->map(function ($value) {
            return is_object($value) && method_exists($value, 'toArray') ? $value->toArray() : $value;
        })->all();
    }

    /**
     * Get a value retrieving callback.
     * @param callable|string|null $value
     * @return callable
     */
    protected function valueRetriever($value): callable
    {
        return function ($item) use ($value) {
            return $this->get($item, $value);
        };
    }

    /**
     * Return the default value of the given value.
     *
     * @param mixed $value
     * @return mixed
     */
    protected function value($value, ...$args)
    {
        return $value instanceof Closure ? $value(...$args) : $value;
    }


    /**
     * Get an item from an array or object using "dot" notation.
     * @param mixed $target
     * @param string|array|int|null $key
     * @param mixed $default
     * @return mixed
     */
    protected function get($target, $key, $default = null)
    {
        $key = is_array($key) ? $key : explode('.', $key);
        if (is_null($key)) {
            return $target;
        }

        foreach ($key as $i => $segment) {
            unset($key[$i]);

            if (is_null($segment)) {
                return $target;
            }

            if (is_array($target) && array_key_exists($segment, $target)) {
                $target = $target[$segment];
            } elseif (is_object($target) && isset($target->{$segment})) {
                $target = $target->{$segment};
            } else {
                return $this->value($default);
            }
        }

        return $target;
    }

    /**
     * Make a function that returns what's passed to it.
     */
    protected function closure(): Closure
    {
        return function ($value) {
            return $value;
        };
    }

    /**
     * Get an iterator for the items.
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->items);
    }

    /**
     * Get an operator checker callback.
     * @param string|null $operator
     * @param mixed $value
     */
    protected function operatorForWhere(string $key, string $operator = null, $value = null): Closure
    {
        // Allow shorthands
        if (func_num_args() === 1) {
            $value = true;
            $operator = '=';
        }
        if (func_num_args() === 2) {
            $value = $operator;
            $operator = '=';
        }

        return function ($item) use ($key, $operator, $value) {
            $retrieved = $this->get($item, $key);

            switch ($operator) {
                default:
                case '=':
                case '==':
                    return $retrieved == $value;
                case '!=':
                case '<>':
                    return $retrieved != $value;
                case '<':
                    return $retrieved < $value;
                case '>':
                    return $retrieved > $value;
                case '<=':
                    return $retrieved <= $value;
                case '>=':
                    return $retrieved >= $value;
                case '===':
                    return $retrieved === $value;
                case '!==':
                    return $retrieved !== $value;
            }
        };
    }
}