<?php

list(, $searchName, $limit) = $argv + array(NULL, '', 8);

// Include the autoloader
include(__DIR__ . '/../classes/Bootstrap.php');
include(__DIR__ . '/Collection.php');


function buildTrie($fileName) {
    $playerData = json_decode(
        file_get_contents($fileName)
    );

    $trie = new \Tries\RadixTrie();
    foreach($playerData as $player) {
        $playerName = $player->surname . ', ' . $player->firstname;
        $trie->add(strtolower($playerName), $player, false);
    }
    return $trie;
}


/* Populate the trie  */
$startTime = microtime(true);

$playerTrie = buildTrie(__DIR__ . '/../data/RugbyData.json');

$endTime = microtime(true);
$callTime = $endTime - $startTime;

echo 'Load Time: ', sprintf('%.4f',$callTime), ' s', PHP_EOL;
echo 'Current Memory: ', sprintf('%.2f',(memory_get_usage(false) / 1024 )), ' k', PHP_EOL;
echo 'Peak Memory: ', sprintf('%.2f',(memory_get_peak_usage(false) / 1024 )), ' k', PHP_EOL, PHP_EOL;


/* Search for the requested names */
$startTime = microtime(true);

$players = new Collection;
foreach ($playerTrie->search(strtolower($searchName)) as $key => $value) {
    $players->add(new Tries\TrieEntry($key, $value));
}

if ($players->count() == 0) {
    echo 'No matches found', PHP_EOL;
} else {
    foreach($players->limit($limit) as $player) {
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
