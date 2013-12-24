Tries
======

A PHP implementation of the Trie and RadixTrie data structures

The /examples folder has two examples to demonstrate their use:

 - playerSearch.php
 - playerSearchRadixTrie.php

    allows a search on Wigan Warriors rugby league players based on surname, displaying the record of those that match the entered search criteria
    
    usage:
        php playerSearch \<name> \<limit>
        
        name     the first few characters of the surname you want to
                 search for
        limit    optional (default 8) limits the number of results
                 returned

 - wordSearch.php
 - wordSearchRadixTrie.php

    searches an English dictionary for words, displaying those that match the entered search criteria

    usage:
        php wordSearch \<searchterm> \<limit>
   
        searchterm   can be a prefix*
                         e.g. "aba*" will return words beginning with "aba"
                     a *suffix
                         e.g. "*ose" will return words ending in "ose"
                     or a split*search criteria
                         e.g. "t*ly" will return words beginning with "t" 
                              and ending in "ly"
        limit        optional (default 8) limits the number of results
                     returned

    Note that the dictionary of 160k words will take some time to load, depending on your system
