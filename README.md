Tries
======

A PHP implementation of the Trie, RadixTrie and SuffixTrie data structures

Master: [![Build Status](https://travis-ci.org/MarkBaker/Tries.png?branch=master)](http://travis-ci.org/MarkBaker/Tries)

Develop: [![Build Status](https://travis-ci.org/MarkBaker/Tries.png?branch=develop)](http://travis-ci.org/MarkBaker/Tries)

## Requirements
 * PHP version 7.2 or higher


## Installation

We recommend installing this package with [Composer](https://getcomposer.org/ "Get Composer").

### Via composer

In your project root folder, execute

```
composer require markbaker/tries:dev-master
```

You should now have the files `composer.json` and `composer.lock` as well as the directory `vendor` in your project directory.

You can then require the Composer autoloader from your code

```
require 'vendor/autoload.php';
```


Or, if you already have a composer.json file, then require this package in that file

```
"require": {
    "markbaker/tries": "dev-master"
}
```

and update composer.

```
composer update
```

### From Phar

Although we strongly recommend using Composer, we also provide a [Phar archive](http://php.net/manual/en/book.phar.php "Read about Phar") builder that will create a Phar file containing all of the library code.

The phar builder script is in the repository root folder, and can be run using

```
php buildPhar.php
```

To use the archive, just require it from your script:

```
require 'Tries.phar';
```

### Standard Autoloader

If you want to run the code without using composer's autoloader, and don't want to build the phar, then required the `bootstrap.php` file from the repository in your code, and this will enable the autoloader for the library.

```
require 'bootstrap.php';
```


## Want to contribute?
Fork this library!


## License
Tries is licensed under an [MIT LICENSE)](https://github.com/MarkBaker/Tries/blob/master/LICENSE.md)


## Examples

The /examples folder has two examples to demonstrate their use:

 - playerSearch.php
 - playerSearchRadixTrie.php
 - playerSearchSuffixTrie.php

    allows a search on Wigan Warriors rugby league players based on surname, displaying the record of those that match the entered search criteria

    usage:

        php playerSearch <name> <limit>

    or

        php playerSearchRadixTrie <name> <limit>

    or

        php playerSearchSuffixTrie <name> <limit>

    where

        name     the first few characters of the surname you want to
                 search for (or characters from anywhere in the name
                 if using the Suffix Trie search)
        limit    optional (default 8) limits the number of results
                 returned

 - wordSearch.php
 - wordSearchRadixTrie.php

    searches an English dictionary for words, displaying those that match the entered search criteria

    usage:

        php wordSearch <searchterm> <limit>

    or

        php wordSearchRadixTrie <searchterm> <limit>

    where

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
