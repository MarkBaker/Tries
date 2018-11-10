<?php

namespace Tries;

/**
 *
 * Trie interface
 *
 * @package Tries
 * @copyright  Copyright (c) 2013 Mark Baker (https://github.com/MarkBaker/Tries)
 * @license    http://www.gnu.org/licenses/lgpl-3.0.txt    LGPL
 */
interface ITrie
{
    /**
     * Adds a new entry to the Trie
     * If the specified node already exists, then its value will be overwritten
     *
     * @param   mixed   $key     Key for this node entry
     * @param   mixed   $value   Data Value for this node entry
     * @return  null
     * @throws \InvalidArgumentException if the provided key argument is empty
     *
     * @TODO Option to allow multiple values with the same key, perhaps a flag indicating overwrite or
     *          allow duplicate entries
     */
    public function add($key, $value = null);

    /**
     * Delete a node in the Trie
     *
     * @param   mixed   $key   The key for the node that we want to delete
     * @return  boolean        Success or failure, false if the node didn't exist
     */
    public function delete($key);

    /**
     * Check if a node exists within the Trie
     *
     * @param   mixed   $key   The key for the node that we want to check
     * @return  boolean
     */
    public function isNode($key);

    /**
     * Check if a node exists within the Trie, and is a data node
     *
     * @param   mixed   $key   The key for the node that we want to check
     * @return  boolean
     */
    public function isMember($key);

    /**
     * Return an array of key/value pairs for nodes matching a specified prefix
     *
     * @param   mixed   $prefix    The key for the node that we want to return
     * @return  \Generator       Collection of TrieEntry key/value pairs for all child nodes with a value
     */
    public function search($prefix);
}
