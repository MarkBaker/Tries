<?php

list(, $searchTerm, $limit) = $argv + array(NULL, '', 8);

// Include the autoloader
include(__DIR__ . '/../classes/Bootstrap.php');
include(__DIR__ . '/Collection.php');


function buildTries($fileName) {
    echo 'Loading dictionary', PHP_EOL;

    $wordData = json_decode(
        file_get_contents($fileName)
    );

    $trie = new \Tries\Trie();
    $rtrie = new \Tries\Trie();

    $wordCount = 0;
    foreach($wordData as $word) {
        if ($wordCount > 0 && $wordCount % 1000 == 0) echo '.';
        if ($word > '') {
            $trie->add($word);
            $rtrie->add(strrev($word));
            ++$wordCount;
        }
    }
    echo PHP_EOL, "Added $wordCount words from dictionary", PHP_EOL;
    return array('trie' => $trie, 'rtrie' => $rtrie);
}

function searchTries($search, $tries, $limit) {
    $terms = explode('*', $search);
    $termcount = count($terms);
    if($termcount > 2) {
        return false;
    }

    if($termcount == 2 && strlen($terms[0]) && strlen($terms[1])) {
        // middle wildcard
        $straight = new Collection;
        foreach ($tries['trie']->search(strtolower($terms[0])) as $key => $value) {
            $straight->add(new Tries\TrieEntry($key, $value));
        }
        $reversed = new Collection;
        foreach ($tries['rtrie']->search(strrev(strtolower($terms[1]))) as $key => $value) {
            $reversed->add(new Tries\TrieEntry($key, $value));
        }
        $straight->intersect($reversed->reverseKeys());
        return $straight->limit($limit);
    } elseif($termcount == 2 && strlen($terms[1]) ) {
        // leading wildcard
        $reversed = new Collection;
        foreach ($tries['rtrie']->search(strrev(strtolower($terms[1]))) as $key => $value) {
            $reversed->add(new Tries\TrieEntry($key, $value));
        }
        $reversed->reverseKeys();
        return $reversed->limit($limit);
    } else {
        // trailing wildcard
        $straight = new Collection;
        foreach ($tries['trie']->search(strtolower($terms[0])) as $key => $value) {
            $straight->add(new Tries\TrieEntry($key, $value));
        }
        return $straight->limit($limit);
    }
}


/* Populate the trie  */
$startTime = microtime(true);

$tries = buildTries(__DIR__ . '/../data/dictionary.json');

$endTime = microtime(true);
$callTime = $endTime - $startTime;

echo 'Load Time: ', sprintf('%.4f',$callTime), ' s', PHP_EOL;
echo 'Current Memory: ', sprintf('%.2f',(memory_get_usage(false) / 1024 )), ' k', PHP_EOL;
echo 'Peak Memory: ', sprintf('%.2f',(memory_get_peak_usage(false) / 1024 )), ' k', PHP_EOL, PHP_EOL;


/* Search for the requested terms */
$startTime = microtime(true);

$searchResult = searchTries($searchTerm, $tries, $limit);
if ($searchResult->count() == 0) {
    echo 'No matching words found', PHP_EOL;
} else {
    foreach($searchResult as $word => $value) {
        echo $word, PHP_EOL;
    }
}
echo PHP_EOL;

$endTime = microtime(true);
$callTime = $endTime - $startTime;

echo 'Search Time: ', sprintf('%.4f',$callTime), ' s', PHP_EOL;
echo 'Current Memory: ', sprintf('%.2f',(memory_get_usage(false) / 1024 )), ' k', PHP_EOL;
echo 'Peak Memory: ', sprintf('%.2f',(memory_get_peak_usage(false) / 1024 )), ' k', PHP_EOL;
