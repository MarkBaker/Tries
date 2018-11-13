<?php

class Collection implements \Iterator, \Countable
{
    private $position = 0;
    private $entries = [];

    public function __construct()
    {
        $this->position = 0;
    }

    public function count()
    {
        return count($this->entries);
    }

    public function rewind()
    {
        $this->position = 0;
    }

    public function current()
    {
        return $this->entries[$this->position]->value;
    }

    public function key()
    {
        return $this->entries[$this->position]->key;
    }

    public function next()
    {
        ++$this->position;
    }

    public function valid()
    {
        return isset($this->entries[$this->position]);
    }

    public function add(Tries\TrieEntry $value)
    {
        $this->entries[] = $value;
    }

    public function merge(Collection $collection)
    {
        foreach ($collection as $key => $value) {
            $this->add(new Tries\TrieEntry($value, $key));
        }
    }

    public function limit($quantity = 10)
    {
        $this->entries = array_slice(
            $this->entries,
            0,
            $quantity
        );
        return $this;
    }

    public function sortKeys()
    {
        usort(
            $this->entries,
            function ($valueA, $valueB) {
                return strnatcasecmp($valueA->key, $valueB->key);
            }
        );
        return $this;
    }

    public function reverseKeys()
    {
        array_walk(
            $this->entries,
            function (&$value) {
                $value->key = strrev($value->key);
            }
        );
        $this->sortKeys();
        return $this;
    }

    public function getKeys()
    {
        return array_map(
            function ($value) {
                return $value->key;
            },
            $this->entries
        );
    }

    public function intersect(Collection $collection)
    {
        $keys = $collection->getKeys();
        $this->entries = array_values(
            array_filter(
                $this->entries,
                function ($value) use ($keys) {
                    return in_array($value->key, $keys);
                }
            )
        );
        return $this->sortKeys();
    }
}