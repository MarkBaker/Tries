<?php

namespace Tries;

use PHPUnit\Framework\TestCase;

class TrieEntryTest extends TestCase
{
    public function testInstantiate()
    {
        $value = 'Hello';
        
        $trieEntryObject = new TrieEntry($value);
        //    Must return an object...
        $this->assertTrue(is_object($trieEntryObject));
        //    ... of the correct type
        $this->assertTrue(is_a($trieEntryObject, __NAMESPACE__ . '\TrieEntry'));

        $this->assertEquals($value, $trieEntryObject->value);
    }

    public function testInstantiateWithKeyArgument()
    {
        $value = 'Hello';
        $key = 'World';

        $trieEntryObject = new TrieEntry($value, $key);

        $this->assertEquals($value, $trieEntryObject->value);
        $this->assertEquals($key, $trieEntryObject->key);
    }
}
