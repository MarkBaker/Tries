<?php

namespace Tries;

/**
 *
 * Trie class
 *
 * @package Tries
 * @copyright  Copyright (c) 2013 Mark Baker (https://github.com/MarkBaker/Tries)
 * @license    http://www.gnu.org/licenses/lgpl-3.0.txt    LGPL
 */
class Trie implements ITrie
{

    /**
     * Root-level TrieNode
     *
     * @var   TrieNode[]
     **/
    protected $trie;

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
            $trieNodeEntry = $this->getTrieNodeByKey($key, true);
            $trieNodeEntry->valueNode = true;
            if ($trieNodeEntry->value === null) {
                $trieNodeEntry->value = array($value);
            } else {
                $trieNodeEntry->value[] = $value;
            }
        } else {
            throw new \InvalidArgumentException('Key value must not be empty');
        }
    }

    /**
     * Backtrack toward the root of the Trie, deleting as we go, until we reach a node that we shouldn't delete
     *
     * @param   TrieNode   $trieNode   This node entry
     * @param   mixed       $key        The full key for this node entry
     * @return  null
     */
    protected function deleteBacktrace(TrieNode $trieNode, $key)
    {
        $previousKey = substr($key, 0, -1);
        $thisChar = substr($key, -1);
        $previousTrieNode = $this->getTrieNodeByKey($previousKey);
        unset($previousTrieNode->children[$thisChar]);

        if ((count($previousTrieNode->children) == 0) && (!$previousTrieNode->valueNode)) {
            $this->deleteBacktrace($previousTrieNode, $previousKey);
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
            $trieNode->valueNode = false;
            $trieNode->value = null;
        } else {
            $this->deleteBacktrace($trieNode, $key);
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

        return $trieNode !== false && $trieNode->valueNode;
    }

    /**
     * Return an array of key/value pairs for nodes matching a specified prefix
     *
     * @param   mixed   $prefix    The key for the node that we want to return
     * @return  TrieCollection[]   Collection of TrieEntry key/value pairs for all child nodes with a value
     */
    public function search($prefix)
    {
        $trieNode = $this->getTrieNodeByKey($prefix);
        if (!$trieNode) {
            return new TrieCollection();
        }
        return $this->getAllChildren($trieNode, $prefix);
    }

    /**
     * Fetch a node that exists at the specified key, or false if it doesn't exist
     *
     * @param   mixed     $key       The key for the node that we want to find
     * @param   boolean   $create    Flag indicating if we should create new nodes in the Trie as we traverse it
     * @return  TrieNode | boolean   False if the specified node doesn't exist, and not flagged to create
     */
    protected function getTrieNodeByKey($key, $create = false)
    {
        $trieNode = $this->trie;
        $keyLen = strlen($key);

        $i = 0;
        while ($i < $keyLen) {
            $character = $key[$i];
            if ($trieNode->children === null) {
                $trieNode->children = array();
            }
            if (!isset($trieNode->children[$character])) {
                if ($create) {
                    $trieNode->children[$character] = new TrieNode();
                } else {
                    return false;
                }
            }
            $trieNode = $trieNode->children[$character];
            ++$i;
        };

        return $trieNode;
    }

    /**
     * Fetch all child nodes with a value below a specified node
     *
     * @param   TrieNode   $trieNode   Node that is our start point for the retrieval
     * @param   mixed      $prefix     Full Key for the requested start point
     * @return  TrieCollection[]       Collection of TrieEntry key/value pairs for all child nodes with a value
     */
    protected function getAllChildren(TrieNode $trieNode, $prefix)
    {
        $collection = new TrieCollection();
        if ($trieNode->valueNode) {
            foreach($trieNode->value as $value) {
                if ($value instanceOf TrieEntry) {
                    $collection->add(clone $value);
                } else {
                    $collection->add(
                        new TrieEntry($value, $prefix)
                    );
                }
            }
        }

        if (isset($trieNode->children)) {
            foreach ($trieNode->children as $character => $trie) {
                $collection->merge(
                    $this->getAllChildren($trie, $prefix . $character)
                );
            }
        }

        return $collection;
    }
}
