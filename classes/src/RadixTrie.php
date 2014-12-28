<?php

namespace Tries;

/**
 *
 * RadixTrie class
 *
 * @package Tries
 * @copyright  Copyright (c) 2013 Mark Baker (https://github.com/MarkBaker/Tries)
 * @license    http://www.gnu.org/licenses/lgpl-3.0.txt    LGPL
 */
class RadixTrie implements ITrie
{
    /** 
     * Root-level TrieNode
     *
     * @var   TrieNode[]
     **/
    private $trie;

    public function __construct()
    {
        $this->trie = new TrieNode();
    }

    /**
     * Adds a new entry to the RadixTrie
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
            $trieNodeEntry = $this->getTrieNodeByKey($this->trie, $key, true);
            $trieNodeEntry->valueNode = true;
            $trieNodeEntry->value = $value;
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
        if ((!$trieNode) || (!$trieNode['node']->valueNode)) {
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

        return $trieNode !== false && $trieNode->valueNode;
    }

    /**
     * Return an array of key/value pairs for nodes matching a specified prefix
     *
     * @param   mixed   $prefix   The key for the node that we want to return
     * @return  mixed[]           Array of key/value pairs for all child nodes with a value
     */
    public function search($prefix)
    {
        $trieNode = $this->findTrieNodeByKey($this->trie, $prefix);
        if (!$trieNode) {
            return false;
        }

        return $this->getAllChildren(
            $trieNode['node'],
            $prefix,
            substr(
                $prefix,
                0,
                strlen($prefix) - strlen($trieNode['key'])
            )
        );
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
        if (empty($trieNode->children)) {
            return array(
                'node' => $trieNode,
                'key' => ''
            );
        }

        $i = 1;
        while ($i <= $keyLen) {
            $characters = substr($key, 0, $i);
            if (isset($trieNode->children[$characters])) {
                $nestedTrieNode = $trieNode->children[$characters];
                if ($i == $keyLen) {
                    return array(
                        'node' => $nestedTrieNode,
                        'key' => ''
                    );
                }
                $key = substr($key, $i);
                return $this->findTrieNodeByKey($nestedTrieNode, $key);
            }
            ++$i;
        };
        return array(
            'node' => $trieNode,
            'key' => $key
        );
    }

    /**
     * Fetch a node that exists at the specified key, or false if it doesn't exist
     *
     * @param   TrieNode  $trieNode   Starting node for the search
     * @param   mixed     $key        The key for the node that we want to find
     * @param   boolean   $create     Flag indicating if we should create new nodes in the RadixTrie as we traverse it
     * @return  TrieNode | boolean    False if the specified node doesn't exist, and not flagged to create
     */
    protected function getTrieNodeByKey(TrieNode $trieNode, $key, $create = false)
    {
        $keyLen = strlen($key);

        if (empty($trieNode->children)) {
            if ($create) {
                $trieNode->children[$key] = new TrieNode();
                return $trieNode->children[$key];
            } else {
                return false;
            }
        }

        $i = 1;
        while ($i <= $keyLen) {
            $characters = substr($key, 0, $i);
            if (isset($trieNode->children[$characters])) {
                $nestedTrieNode = $trieNode->children[$characters];
                if ($i == $keyLen) {
                    return $nestedTrieNode;
                }
                $key = substr($key, $i);
                return $this->getTrieNodeByKey($nestedTrieNode, $key, $create);
            }
            ++$i;
        };

        if ($i >= $keyLen) {
            if ($create) {
                $found = false;
                foreach ($trieNode->children as $trieNodekey => $child) {
                    $i = 1;
                    while (substr($key, 0, $i) == substr($trieNodekey, 0, $i)) {
                        ++$i;
                        $found = true;
                        $splitTrieNode = $child;
                        $splitTrieKey = substr($trieNodekey, 0, $i-1);
                    }
                    if ($found) {
                        break;
                    }
                }
                if (!$found) {
                    $trieNode->children[$key] = new TrieNode();
                    return $trieNode->children[$key];
                } else {
                    --$i;
                    $newTrieNode = new TrieNode();
                    $characters = substr($characters, $i);
                    if ($characters !== false) {
                        $newTrieNode->children[$characters] = new TrieNode();
                    }
                    $newSplitKey = substr($trieNodekey, $i);
                    $newTrieNode->children[$newSplitKey] = $splitTrieNode;
                    $trieNode->children[$splitTrieKey] = $newTrieNode;
                    unset($trieNode->children[$trieNodekey]);
                    if ($characters !== false) {
                        return $newTrieNode->children[$characters];
                    } else {
                        return $newTrieNode;
                    }
                }
            } else {
                return false;
            }
        }
    }

    /**
     * Fetch all child nodes with a value below a specified node
     *
     * @param   TrieNode   $trieNode        Node that is our start point for the retrieval
     * @param   string     $searchPrefix    The prefix that we're searching for, so we can elimi
     * @param   string     $prefix          Full Key for the requested start point
     * @return  mixed[]                     Array of key/value pairs for all child nodes with a value
     */
    protected function getAllChildren(TrieNode $trieNode, $searchPrefix, $prefix)
    {
        $return = array();
        if ($trieNode->valueNode) {
            if (strpos($prefix, $searchPrefix) === 0) {
                $return[$prefix] = $trieNode->value;
            }
        }

        if (isset($trieNode->children)) {
            foreach ($trieNode->children as $characters => $trie) {
                $return = array_merge(
                    $return,
                    $this->getAllChildren($trie, $searchPrefix, $prefix . $characters)
                );
            }
        }

        return $return;
    }
}
