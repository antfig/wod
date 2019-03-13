<?php
declare(strict_types=1);

namespace Wod;

class Collection implements \Iterator, \Countable
{
    /**
     * The collection's encapsulated array
     *
     * @var array
     */
    protected $items;

    /**
     * @param array $items
     */
    public function __construct(array $items = [])
    {
        $this->items = $items;
    }

    // Iterator

    /**
     * @inheritdoc
     */
    public function current()
    {
        return current($this->items);
    }

    /**
     * @inheritdoc
     */
    public function key()
    {
        return key($this->items);
    }

    /**
     * @inheritdoc
     */
    public function next()
    {
        next($this->items);
    }

    /**
     * @inheritdoc
     */
    public function rewind()
    {
        reset($this->items);
    }

    /**
     * @inheritdoc
     */
    public function valid()
    {
        return $this->key() !== null;
    }

    /**
     * @inheritdoc
     */
    public function count()
    {
        return count($this->items);
    }

    // Custom methods

    public function toArray(): array
    {
        return $this->items;
    }

    /**
     * Appends an item to the end of the collection
     *
     * @param mixed $value
     *
     * @return Collection
     */
    public function add($value): Collection
    {
        $this->items[] = $value;

        return $this;
    }

    /**
     * Sets the given key and value in the collection
     *
     * @param mixed $key
     * @param mixed $value
     *
     * @return Collection
     */
    public function set($key, $value): Collection
    {
        $this->items[$key] = $value;

        return $this;
    }

    /**
     * Get an item from the collection
     *
     * @param mixed $key
     * @param mixed $default [optional]
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return $this->items[$key] ?? $default;
    }

    /**
     * Remove all the items from the collection
     */
    public function clear()
    {
        $this->items = [];
    }

    /**
     * Get Random item from the collection
     * @return mixed
     */
    public function random()
    {
        $randomKey = array_rand($this->items, 1);
        return $this->items[$randomKey];
    }

    /**
     * Run a filter over each of the items
     * Array keys are preserved
     *
     * @param callable $callback
     *
     * @return Collection
     */
    public function filter(callable $callback): self
    {
        return new static(array_filter($this->items, $callback, ARRAY_FILTER_USE_BOTH));
    }

    /**
     * @param mixed $value
     *
     * @return bool
     */
    public function hasValue($value): bool
    {
        return in_array($value, $this->items);
    }
}
