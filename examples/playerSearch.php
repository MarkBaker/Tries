<?php

list(, $searchName, $limit) = $argv + array(NULL, '', 8);

include('../src/TrieAutoloader.php');


function buildTries($fileName) {
    $playerData = json_decode(
        file_get_contents($fileName)
    );

    $trie = new \Tries\Trie();
    foreach($playerData as $player) {
        $playerName = $player->surname . ', ' . $player->firstname;
        $trie->add(strtolower($playerName), $player);
    }
    return $trie;
}


/* Populate the trie  */
$startTime = microtime(true);

$tries = buildTries(__DIR__ . '/../data/RugbyData.json');

$endTime = microtime(true);
$callTime = $endTime - $startTime;

echo 'Load Time: ', sprintf('%.4f',$callTime), ' s', PHP_EOL;
echo 'Current Memory: ', sprintf('%.2f',(memory_get_usage(false) / 1024 )), ' k', PHP_EOL;
echo 'Peak Memory: ', sprintf('%.2f',(memory_get_peak_usage(false) / 1024 )), ' k', PHP_EOL, PHP_EOL;


/* Search for the requested names */
$startTime = microtime(true);

$searchResult = $tries->search(strtolower($searchName));
if (empty($searchResult)) {
    echo 'No matches found', PHP_EOL;
} else {
    $players = array_slice($searchResult, 0, $limit);
    foreach($players as $player) {
        echo sprintf(
            '%s, %s, (%s)', 
            $player->surname, $player->firstname, $player->seasons
        ), PHP_EOL;
        echo sprintf(
            '    Starts: %d, Substitutions: %d, Appearances: %d', 
            $player->starts, $player->substitutions, $player->appearances
        ), PHP_EOL;
        echo sprintf(
            '    Tries: %d, Goals: %d, Field Goals: %d, Points Scored: %d', 
            $player->tries, $player->goals, $player->fieldgoals, $player->points
        ), PHP_EOL;
    }
}
echo PHP_EOL;

$endTime = microtime(true);
$callTime = $endTime - $startTime;

echo 'Search Time: ', sprintf('%.4f',$callTime), ' s', PHP_EOL;
echo 'Current Memory: ', sprintf('%.2f',(memory_get_usage(false) / 1024 )), ' k', PHP_EOL;
echo 'Peak Memory: ', sprintf('%.2f',(memory_get_peak_usage(false) / 1024 )), ' k', PHP_EOL;
