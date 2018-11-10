<?php

// Include the autoloader
include(__DIR__ . '/../classes/Bootstrap.php');

function getData()
{
    return [
        ['cat', 'n. felix domesticus'],
        ['car', 'n. A motor vehicle'],
        ['cart', 'v. To carry awkwardly'],
        ['cart', 'n. A vehicle for carrying things'],
        ['card', 'n. A piece of thick paper'],
        ['cardigan', 'n. A jumper'],
    ];
}

function populateTrie() {
    $trie = new Tries\Trie();
    foreach (getData() as [$key, $value]) {
        $trie->add($key, $value);
    }
    return $trie;
}

function populateRadixTrie() {
    $trie = new Tries\RadixTrie();
    foreach (getData() as [$key, $value]) {
        $trie->add($key, $value);
    }
    return $trie;
}

function populateSuffixTrie() {
    $trie = new Tries\SuffixTrie();
    foreach (getData() as [$key, $value]) {
        $trie->add($key, $value);
    }
    return $trie;
}

function searchTrie($trie, $prefix)
{
    foreach ($trie->search($prefix) as $key => $value) {
        echo $key, ' => ', $value, PHP_EOL;
    }
}

$trie = populateTrie();
searchTrie($trie, 'car');
echo PHP_EOL;

$radixTrie = populateRadixTrie();
searchTrie($radixTrie, 'car');
echo PHP_EOL;

$suffixTrie = populateSuffixTrie();
searchTrie($suffixTrie, 'ar');
echo PHP_EOL;
