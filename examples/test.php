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
        ['carp', 'v. To complain'],
        ['carp', 'n. A species of fish, not good with chips'],
        ['carpenter', 'n. A person that mends carps'],
        ['carpentry', 'n. Complaints by a carpenter'],
        ['pen', 'A writer'],
    ];
}

function populateTrie() {
    $trie = new Tries\Trie();
    foreach (getData() as $entry) {
        $trie->add(...$entry);
    }
    return $trie;
}

function populateRadixTrie() {
    $trie = new Tries\RadixTrie();
    foreach (getData() as $entry) {
        $trie->add(...$entry);
    }
    return $trie;
}

function populateSuffixTrie() {
    $trie = new Tries\SuffixTrie();
    foreach (getData() as $entry) {
        $trie->add(...$entry);
    }
    return $trie;
}

function searchTrie(Tries\ITrie $trie, $prefix)
{
    foreach ($trie->search($prefix) as $key => $value) {
        echo $key, ' => ', $value, PHP_EOL;
    }
}

$trie = populateTrie();
searchTrie($trie, 'carp');
echo PHP_EOL;

$radixTrie = populateRadixTrie();
searchTrie($radixTrie, 'carp');
echo PHP_EOL;

$suffixTrie = populateSuffixTrie();
searchTrie($suffixTrie, 'arp');
echo PHP_EOL;
