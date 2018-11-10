<?php

namespace Tries;

use PHPUnit\Framework\TestCase;

class TrieEntryTest extends TestCase
{
    public function testInstantiate()
    {
        $key = 'Hello';
        
        $trieEntryObject = new TrieEntry($key);
        //    Must return an object...
        $this->assertTrue(is_object($trieEntryObject));
        //    ... of the correct type
        $this->assertTrue(is_a($trieEntryObject, __NAMESPACE__ . '\TrieEntry'));

        $this->assertEquals($key, $trieEntryObject->key);
    }

    public function testInstantiateWithKeyArgument()
    {
        $key = 'Hello';
        $value = 'World';

        $trieEntryObject = new TrieEntry($key, $value);

        $this->assertEquals($key, $trieEntryObject->key);
        $this->assertEquals($value, $trieEntryObject->value);
    }
}
