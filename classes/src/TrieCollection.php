<?php

namespace Tries;

/**
 *
 * TrieCollection class
 *
 * @package Tries
 * @copyright  Copyright (c) 2013 Mark Baker (https://github.com/MarkBaker/Tries)
 * @license    http://www.gnu.org/licenses/lgpl-3.0.txt    LGPL
 */
class TrieCollection implements \Iterator, \Countable {
    private $position = 0;
    private $entries = [];  

    public function __construct() {
        $this->position = 0;
    }

    public function count() { 
        return count($this->entries); 
    }

    function rewind() {
        $this->position = 0;
    }

    function current() {
        return $this->entries[$this->position]->value;
    }

    function key() {
        return $this->entries[$this->position]->key;
    }

    function next() {
        ++$this->position;
    }

    function valid() {
        return isset($this->entries[$this->position]);
    }

    public function add(TrieEntry $value) { 
        $this->entries[] = $value;
    }

    public function merge(TrieCollection $collection) { 
        foreach($collection as $key => $value) {
            $this->add(new TrieEntry($value, $key));
        }
    }

    public function limit($quantity=10) {
        $this->entries = array_slice(
            $this->entries,
            0,
            $quantity
        );
        return $this;
    }

    public function sortKeys() {
        usort(
            $this->entries,
            function ($a, $b) {
                return strnatcasecmp($a->key, $b->key);
            }
        );
        return $this;
    }

    public function reverseKeys() {
        array_walk(
            $this->entries,
            function (&$value) {
                $value->key = strrev($value->key);
            }
        );
        $this->sortKeys();
        return $this;
    }

    public function getKeys() {
        return array_map(
            function ($value) {
                return $value->key;
            },
            $this->entries
        );
    }

    public function intersect(TrieCollection $collection) {
        $keys = $collection->getKeys();
        $this->entries = array_values(
            array_filter(
                $this->entries,
                function($value) use ($keys) {
                    return in_array($value->key, $keys);
                }
            )
        );
        return $this->sortKeys();
    }
}
