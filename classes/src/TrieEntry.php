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
    public function __construct($key, $value = null)
    {
        $this->key = $key;
        $this->value = $value;
    }

    public function __toString()
    {
        return $this->value;
    }
}
