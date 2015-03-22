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
class SuffixTrie extends Trie implements ITrie
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
    public function add($key, $value = null)
    {
        if ($key > '') {
            $keyLength = strlen($key);
            $suffix = $key;
            $i = 0;
            $data = new TrieEntry($value, $key);
            while(++$i <= $keyLength) {
                $trieNodeEntry = $this->getTrieNodeByKey($suffix, true);
                $trieNodeEntry->valueNode = true;
                if ($trieNodeEntry->value === null) {
                    $trieNodeEntry->value = array($data);
                } else {
                    $trieNodeEntry->value[] = $data;
                }
                $suffix = substr($suffix, 1);
            }
        } else {
            throw new \InvalidArgumentException('Key value must not be empty');
        }
    }
}
