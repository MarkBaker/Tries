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
            $data = new TrieEntry($value, $key);
            do {
                parent::add($key, $data);
                $key = substr($key, 1);
            } while ($key > '');
        } else {
            throw new \InvalidArgumentException('Key value must not be empty');
        }
    }
}
