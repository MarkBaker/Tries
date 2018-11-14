<?php

namespace Tries;

use Generator;

/**
 *
 * RadixTrie class
 *
 * @package Tries
 * @copyright  Copyright (c) 2013 Mark Baker (https://github.com/MarkBaker/Tries)
 * @license    https://opensource.org/licenses/MIT    MIT
 */
class RadixTrie implements ITrie
{
    /**
     * Root-level TrieNode
     *
     * @var   TrieNode[]
     **/
    protected $trie = null;

    /**
     * Create a new Radix Trie
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
            $trieNode = $this->createTrieNodeByKey($this->trie, $key);
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
     * Delete a node in the RadixTrie
     *
     * @param   mixed   $key   The key for the node that we want to delete
     * @return  boolean        Success or failure, false if the node didn't exist
     */
    public function delete($key)
    {
        $trieNode = $this->findTrieNodeByKey($this->trie, $key);
        if ((!$trieNode) || ($trieNode['node']->value !== null)) {
            return false;
        }

        // Find the parent node for our current node
        $parentNode = $this->findTrieNodeByKey($this->trie, substr($key, 0, -1));
        $newBaseKey = $parentNode['key'] . substr($key, -1);

        // Attach any children of the node we're deleting as children of its parent node
        //    adjusting the child node keys as necessary
        foreach ($trieNode['node']->children as $childKey => $value) {
            $parentNode['node']->children[$newBaseKey . $childKey] = $value;
        }
        // Remove the node we want to delete
        unset($parentNode['node']->children[$newBaseKey]);

        return true;
    }

    /**
     * Check if a node exists within the RadixTrie
     *
     * @param   mixed   $key   The key for the node that we want to check
     * @return  boolean
     */
    public function isNode($key)
    {
        $trieNode = $this->getTrieNodeByKey($this->trie, $key);

        return $trieNode !== false;
    }

    /**
     * Check if a node exists within the RadixTrie, and is a data node
     *
     * @param   mixed   $key   The key for the node that we want to check
     * @return  boolean
     */
    public function isMember($key)
    {
        $trieNode = $this->getTrieNodeByKey($this->trie, $key);

        return $trieNode !== false && $trieNode->value !== null;
    }

    /**
     * Fetch a node that exists at the specified key, or the previous node if no exact match exists
     *
     * @param   TrieNode  $trieNode   Starting node for the search
     * @param   mixed     $key        The key for the node that we want to find
     * @return  mixed[]               ['node'] TrieNode    The closest parent match to the specified node
     *                                ['key']  string      Partial key from the nearest parent node to the requested key
     */
    protected function findTrieNodeByKey(TrieNode $trieNode, $key)
    {
        $keyLen = strlen($key);

        $index = 1;
        while ($index <= $keyLen) {
            $characters = substr($key, 0, $index);
            if (isset($trieNode->children[$characters])) {
                $nestedTrieNode = $trieNode->children[$characters];
                if ($index == $keyLen) {
                    return [
                        'node' => $nestedTrieNode,
                        'key' => ''
                    ];
                }
                $key = substr($key, $index);
                return $this->findTrieNodeByKey($nestedTrieNode, $key);
            }
            ++$index;
        };
        return [
            'node' => $trieNode,
            'key' => $key
        ];
    }

    /**
     * Fetch a node that exists at the specified key, or false if it doesn't exist
     *
     * @param   TrieNode  $trieNode   Starting node for the search
     * @param   mixed     $key        The key for the node that we want to find
     * @return  TrieNode
     */
    private function createTrieNodeByKey(TrieNode $trieNode, $key)
    {
        if (empty($trieNode->children)) {
            $trieNode->children[$key] = new TrieNode();
            return $trieNode->children[$key];
        }

        $keyLen = strlen($key);

        $index = 1;
        while ($index <= $keyLen) {
            $characters = substr($key, 0, $index);
            if (isset($trieNode->children[$characters])) {
                $nestedTrieNode = $trieNode->children[$characters];
                if ($index === $keyLen) {
                    return $nestedTrieNode;
                }
                $key = substr($key, $index);
                return $this->createTrieNodeByKey($nestedTrieNode, $key);
            }
            ++$index;
        };

        return $this->splitTrieNode($trieNode, $key, $characters);
    }

    /**
     * @param TrieNode $trieNode
     * @param $key
     * @param $characters
     * @return TrieNode
     */
    private function splitTrieNode(TrieNode $trieNode, $key, $characters) : TrieNode
    {
        $foundNodeToSplit = false;
        foreach ($trieNode->children as $trieNodekey => $child) {
            if (substr($key, 0, 1) != substr($trieNodekey, 0, 1)) {
                continue;
            }
            $foundNodeToSplit = true;
            $splitTrieNode = $child;
            $splitTrieKey = substr($trieNodekey, 0, 1);

            $index = 2;
            while (substr($key, 0, $index) == substr($trieNodekey, 0, $index)) {
                $splitTrieKey = substr($trieNodekey, 0, $index++);
            }
        }

        if (!$foundNodeToSplit) {
            $trieNode->children[$key] = new TrieNode();
            return $trieNode->children[$key];
        }

        --$index;

        $newTrieNode = new TrieNode();
        $characters = substr($characters, $index);
        if ($characters !== false) {
            $newTrieNode->children[$characters] = new TrieNode();
        }
        $newSplitKey = substr($trieNodekey, $index);
        $newTrieNode->children[$newSplitKey] = $splitTrieNode;
        $trieNode->children[$splitTrieKey] = $newTrieNode;
        unset($trieNode->children[$trieNodekey]);
        if ($characters !== false) {
            return $newTrieNode->children[$characters];
        }

        return $newTrieNode;
    }

    /**
     * Fetch a node that exists at the specified key, or false if it doesn't exist
     *
     * @param   TrieNode  $trieNode   Starting node for the search
     * @param   mixed     $key        The key for the node that we want to find
     * @return  TrieNode|false   False if the specified node doesn't exist
     */
    private function getTrieNodeByKey(TrieNode $trieNode, $key)
    {
        if (empty($trieNode->children)) {
            return false;
        }

        $keyLen = strlen($key);

        $index = 1;
        while ($index <= $keyLen) {
            $characters = substr($key, 0, $index);
            if (isset($trieNode->children[$characters])) {
                $nestedTrieNode = $trieNode->children[$characters];
                if ($index === $keyLen) {
                    return $nestedTrieNode;
                }
                $key = substr($key, $index);
                return $this->getTrieNodeByKey($nestedTrieNode, $key);
            }
            ++$index;
        };

        return false;
    }

    /**
     * Return an array of key/value pairs for nodes matching a specified prefix
     *
     * @param   mixed   $prefix    The key for the node that we want to return
     * @return  Generator       Collection of TrieEntry key/value pairs for all child nodes with a value
     */
    public function search($prefix) : Generator
    {
        $trieNode = $this->getTrieNodeByKey($this->trie, $prefix);
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
    private function getAllChildren(TrieNode $trieNode, $prefix = '') : Generator
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
