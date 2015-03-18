<?php

namespace Tries;

/**
 *
 * TrieEntry class
 *
 * @package Tries
 * @copyright  Copyright (c) 2013 Mark Baker (https://github.com/MarkBaker/Tries)
 * @license    http://www.gnu.org/licenses/lgpl-3.0.txt    LGPL
 */
class TrieEntry
{
    /**
     * Key for this Trie entry
     *
     * @var   string
     **/
    public $key;

    /**
     * Value for this Trie entry
     *
     * @var   mixed
     **/
    public $value;

    public function __construct($prefix, $value) {
        $this->key = $prefix;
        $this->value = $value;
    }
}
