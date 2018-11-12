<?php

namespace Tries;

/**
 *
 * TrieNode class
 *
 * @package Tries
 * @copyright  Copyright (c) 2013 Mark Baker (https://github.com/MarkBaker/Tries)
 * @license    https://opensource.org/licenses/MIT    MIT
 */
class TrieNode
{
    /**
     * Array of child nodes indexed by next character
     *
     * @var   TrieNode[]
     **/
    public $children = [];

    /**
     * Data value (empty unless this is an end node)
     *
     * @var   mixed
     **/
    public $value = null;
}
