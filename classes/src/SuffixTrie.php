<?php

namespace Tries;

use Generator;

/**
 *
 * SuffixTrie class
 *
 * @package Tries
 * @copyright  Copyright (c) 2013 Mark Baker (https://github.com/MarkBaker/Tries)
 * @license    https://opensource.org/licenses/MIT    MIT
 */
class SuffixTrie extends Trie implements ITrie
{

    /**
     * {@inheritdoc}
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
            $value = new TrieEntry($key, $value);
            do {
                parent::add($key, $value);
                $key = substr($key, 1);
            } while ($key > '');
        } else {
            throw new \InvalidArgumentException('Key value must not be empty');
        }
    }

    /**
     * Fetch all child nodes with a value below a specified node
     *
     * @param   TrieNode   $trieNode   Node that is our start point for the retrieval
     * @param   mixed      $prefix     Full Key for the requested start point
     * @return  Generator  Collection of TrieEntry key/value pairs for all child nodes with a value
     */
    protected function getAllChildren(TrieNode $trieNode, $prefix) : Generator
    {
        if ($trieNode->value !== null) {
            foreach ($trieNode->value as $value) {
                yield $value->key => $value->value;
            }
        }

        if (isset($trieNode->children)) {
            foreach ($trieNode->children as $child) {
                yield from $this->getAllChildren($child, $prefix);
            }
        }
    }
}
