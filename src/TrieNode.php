<?php

class TrieNode {
    /**
     * Array of child nodes indexed by next character
     *
     * @var   TrieNode[]
     **/
    public $children = array();

    /**
     * Flag indicating if this node is an end node
     *
     * @var   boolean
     **/
    public $valueNode = false;

    /**
     * Data value (empty unless this is an end node)
     *
     * @var   mixed
     **/
    public $value;
}

