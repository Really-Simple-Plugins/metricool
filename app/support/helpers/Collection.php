<?php

namespace Metricool\Helpers;

use Closure;
use IteratorAggregate;
use ArrayIterator;

class Collection implements IteratorAggregate
{
    /**
     * The items contained in the collection.
     */
    protected $items = [];

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
     * Get the sum of the given values.
     * @param  callable|string|null  $callback
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
     * @param  mixed  $initial
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
     * Return the default value of the given value.
     *
     * @param  mixed  $value
     * @return mixed
     */
    protected function value($value, ...$args)
    {
        return $value instanceof Closure ? $value(...$args) : $value;
    }

    /**
     * Filter items by the given key value pair.
     *
     * @param  mixed  $operator
     * @param  mixed  $value
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
            return is_object($value) && method_exists('toArray', $value) ? $value->toArray() : $value;
        })->all();
    }

    /**
     * Get a value retrieving callback.
     *
     * @param  callable|string|null  $value
     * @return callable
     */
    protected function valueRetriever($value): callable
    {
        return function ($item) use ($value) {
            return $this->get($item, $value);
        };
    }

    /**
     * Get an item from an array or object using "dot" notation.
     *
     * @param  mixed  $target
     * @param  string|array|int|null  $key
     * @param  mixed  $default
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
     * Run a filter over each of the items.
     *
     * @param  callable|null  $callback
     */
    public function filter(callable $callback = null): self
    {
        if ($callback) {
            return new static(array_filter($this->items, $callback, ARRAY_FILTER_USE_BOTH));
        }

        return new static(array_filter($this->items));
    }

    /**
     * Get an operator checker callback.
     *
     * @param  string|null  $operator
     * @param  mixed  $value
     */
    protected function operatorForWhere(string $key, string $operator = null, $value = null): Closure
    {
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
                case '==':  return $retrieved == $value;
                case '!=':
                case '<>':  return $retrieved != $value;
                case '<':   return $retrieved < $value;
                case '>':   return $retrieved > $value;
                case '<=':  return $retrieved <= $value;
                case '>=':  return $retrieved >= $value;
                case '===': return $retrieved === $value;
                case '!==': return $retrieved !== $value;
            }
        };
    }
}