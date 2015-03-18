<?php

list(, $searchTerm, $limit) = $argv + array(NULL, '', 8);

include('../classes/Bootstrap.php');


function buildTries($fileName) {
    echo 'Loading dictionary', PHP_EOL;

    $wordData = json_decode(
        file_get_contents($fileName)
    );

    $trie = new \Tries\RadixTrie();
    $rtrie = new \Tries\RadixTrie();

    $wordCount = 0;
    foreach($wordData as $word) {
        if ($wordCount > 0 && $wordCount % 1000 == 0) echo '.';
        if ($word > '') {
            $trie->add($word, $word);
            $rtrie->add(strrev($word), $word);
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
        $reversed = $tries['rtrie']->search(strrev(strtolower($terms[1])))
            ->reverseKeys();
        return $tries['trie']->search(strtolower($terms[0]))
            ->intersect($reversed)
            ->limit($limit);
    } elseif($termcount == 2 && strlen($terms[1]) ) {
        // leading wildcard
        return $tries['rtrie']->search(strrev(strtolower($terms[1])))
            ->reverseKeys()
            ->limit($limit);
    } else {
        // trailing wildcard
        return $tries['trie']->search(strtolower($terms[0]))
            ->limit($limit);
        return $straight;
    }
}


/* Populate the trie  */
$startTime = microtime(true);

$tries = buildTries(__DIR__ . '/../data/dictionary.json');

$endTime = microtime(true);
$callTime = $endTime - $startTime;

echo 'Load Time: ', sprintf('%.4f',$callTime), ' s', PHP_EOL;
echo 'Current Memory: ', sprintf('%.2f',(memory_get_usage(false) / 1024 )), ' k', PHP_EOL;
echo 'Peak Memory: ', sprintf('%.2f',(memory_get_peak_usage(false) / 1024 )), ' k', PHP_EOL;


/* Search for the requested terms */
$startTime = microtime(true);

$words = searchTries($searchTerm, $tries, $limit);
if (count($words) > 0) {
    foreach($words as $word) {
        echo $word, PHP_EOL;
    }
} else {
    echo 'No matches found', PHP_EOL;
}
echo PHP_EOL;

$endTime = microtime(true);
$callTime = $endTime - $startTime;

echo 'Search Time: ', sprintf('%.4f',$callTime), ' s', PHP_EOL;
echo 'Current Memory: ', sprintf('%.2f',(memory_get_usage(false) / 1024 )), ' k', PHP_EOL;
echo 'Peak Memory: ', sprintf('%.2f',(memory_get_peak_usage(false) / 1024 )), ' k', PHP_EOL;
