<?php

namespace Tries;

use Generator;

/**
 *
 * Trie class
 *
 * @package Tries
 * @copyright  Copyright (c) 2013 Mark Baker (https://github.com/MarkBaker/Tries)
 * @license    https://opensource.org/licenses/MIT    MIT
 */
class Trie implements ITrie
{
    /**
     * Root-level TrieNode
     *
     * @var   TrieNode[]
     */
    protected $trie = null;

    /**
     * Create a new Trie
     */
    public function __construct()
    {
        $this->trie = new TrieNode();
    }

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
    public function add($key, $value = null)
    {
        if ($key > '') {
            $trieNode = $this->createTrieNodeByKey($key);
            if ($trieNode->value === null) {
                $trieNode->value = [$value];
            } else {
                $trieNode->value[] = $value;
            }
        } else {
            throw new \InvalidArgumentException('Key value must not be empty');
        }
    }

    /**
     * Backtrack toward the root of the Trie, deleting as we go, until we reach a node that we shouldn't delete
     *
     * @param   mixed       $key        The full key for this node entry
     * @return  null
     */
    private function deleteBacktrace($key)
    {
        $previousKey = substr($key, 0, -1);
        $thisChar = substr($key, -1);
        $previousTrieNode = $this->getTrieNodeByKey($previousKey);
        unset($previousTrieNode->children[$thisChar]);

        if ((count($previousTrieNode->children) == 0) && ($previousTrieNode->value === null)) {
            $this->deleteBacktrace($previousKey);
        }
    }

    /**
     * Delete a node in the Trie
     *
     * @param   mixed   $key   The key for the node that we want to delete
     * @return  boolean        Success or failure, false if the node didn't exist
     */
    public function delete($key)
    {
        $trieNode = $this->getTrieNodeByKey($key);
        if (!$trieNode) {
            return false;
        }

        if (!empty($trieNode->children)) {
            $trieNode->value = null;
        } else {
            $this->deleteBacktrace($key);
        }

        return true;
    }

    /**
     * Check if a node exists within the Trie
     *
     * @param   mixed   $key   The key for the node that we want to check
     * @return  boolean
     */
    public function isNode($key)
    {
        $trieNode = $this->getTrieNodeByKey($key);

        return $trieNode !== false;
    }

    /**
     * Check if a node exists within the Trie, and is a data node
     *
     * @param   mixed   $key   The key for the node that we want to check
     * @return  boolean
     */
    public function isMember($key)
    {
        $trieNode = $this->getTrieNodeByKey($key);

        return $trieNode !== false && $trieNode->value !== null;
    }

    /**
     * Create a node for the specified key
     *
     * @param   mixed     $key              The key for the node that we want to find
     * @return  TrieNode
     */
    private function createTrieNodeByKey($key)
    {
        $trieNode = $this->trie;
        $keyLen = strlen($key);

        $index = 0;
        while ($index < $keyLen) {
            $character = $key[$index++];
            if (!isset($trieNode->children[$character])) {
                $trieNode->children[$character] = new TrieNode();
            }
            $trieNode = $trieNode->children[$character];
        };

        return $trieNode;
    }

    /**
     * Fetch a node that exists at the specified key, or false if it doesn't exist
     *
     * @param   mixed     $key              The key for the node that we want to find
     * @return  TrieNode|false   False if the specified node doesn't exist
     */
    private function getTrieNodeByKey($key)
    {
        $trieNode = $this->trie;
        $keyLen = strlen($key);

        $index = 0;
        while ($index < $keyLen) {
            $character = $key[$index++];
            if (!isset($trieNode->children[$character])) {
                return false;
            }
            $trieNode = $trieNode->children[$character];
        };

        return $trieNode;
    }

    /**
     * Return an array of key/value pairs for nodes matching a specified prefix
     *
     * @param   mixed   $prefix    The key for the node that we want to return
     * @return  Generator       Collection of TrieEntry key/value pairs for all child nodes with a value
     */
    public function search($prefix) : Generator
    {
        $trieNode = $this->getTrieNodeByKey($prefix);
        if (!$trieNode) {
            return [];
        }

        yield from $this->getAllChildren($trieNode, $prefix);
    }

    /**
     * Fetch all child nodes with a value below a specified node
     *
     * @param   TrieNode   $trieNode   Node that is our start point for the retrieval
     * @param   mixed      $prefix     Full Key for the requested start point
     * @return  Generator       Collection of TrieEntry key/value pairs for all child nodes with a value
     */
    protected function getAllChildren(TrieNode $trieNode, $prefix) : Generator
    {
        if ($trieNode->value !== null) {
            foreach ($trieNode->value as $value) {
                yield $prefix => $value;
            }
        }

        if (isset($trieNode->children)) {
            foreach ($trieNode->children as $key => $child) {
                yield from $this->getAllChildren($child, $prefix . $key);
            }
        }
    }
}
