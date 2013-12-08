<?php

list(, $searchTerm, $limit) = $argv + array(NULL, '', 8);

include('../src/TrieAutoloader.php');


function buildTries($fileName) {
    echo 'Loading dictionary', PHP_EOL;

    $wordData = json_decode(
        file_get_contents($fileName)
    );

    $trie = new \Tries\Trie();
    $rtrie = new \Tries\Trie();

    $wordCount = 0;
    foreach($wordData as $word) {
        $trie->add($word);
        $trie->add($word);
        $rtrie->add(strrev($word));
        ++$wordCount;
    }
    echo "Added $wordCount words from dictionary", PHP_EOL;
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
        $straight = $tries['trie']->search(strtolower($terms[0]));
        $reversed = $tries['rtrie']->search(strrev(strtolower($terms[1])));
        return array_slice(
            array_intersect_key($straight, reverseArray($reversed)),
            0,
            $limit
        );
    } elseif($termcount == 2 && strlen($terms[1]) ) {
        // leading wildcard
        return reverseArray(
            array_slice(
                $tries['rtrie']->search(strrev(strtolower($terms[1]))),
                0,
                $limit
            )
        );
    } else {
        // trailing wildcard
        return array_slice(
            $tries['trie']->search(strtolower($terms[0])),
            0,
            $limit
        );
    }
}

function reverseArray($keys) {
    $return = array();
    foreach($keys as $key => $value) {
        $return[strrev($key)] = $value;
    }
    return $return;
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

$searchResult = searchTries($searchTerm, $tries, $limit);
if (empty($searchResult)) {
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
