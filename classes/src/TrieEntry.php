<?php

namespace Tries;

class TrieEntry
{
    /**
     * The key for this Trie entry
     *
     * @var   string
     **/
    public $key = null;

    /**
     * The Value for this Trie entry
     *
     * @var   mixed
     **/
    public $value = null;

    /**
     * @param mixed $value
     * @param mixed $key
     **/
    public function __construct($value, $key = null)
    {
        $this->value = $value;
        $this->key = $key;
    }

    /**
     * Allows the key for this entry to be reset
     *
     * @param mixed $key
     **/
    public function setKey($key = null)
    {
        $this->key = $key;
    }

    public function __toString()
    {
        return $this->value;
    }
}
