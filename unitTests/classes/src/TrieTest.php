<?php

namespace Tries;

use PHPUnit\Framework\TestCase;

class TrieTest extends TestCase
{
    public function testInstantiate()
    {
        $trieObject = new Trie();
        //    Must return an object...
        $this->assertTrue(is_object($trieObject));
        //    ... of the correct type
        $this->assertTrue(is_a($trieObject, __NAMESPACE__ . '\Trie'));
    }

    public function testAdd()
    {
        $trieObject = new Trie();

        $testData = $this->getTestData();

        foreach ($testData as $entry) {
            $trieObject->add(...$entry);
        }

        // For our test data, 'art' should not be a node
        $this->assertFalse($trieObject->isNode('art'));

        // For our test data, 'ca' should be a node without any values
        $this->assertTrue($trieObject->isNode('ca'));
        $this->assertFalse($trieObject->isMember('ca'));

        foreach ($testData as $entry) {
            $this->assertTrue($trieObject->isNode($entry[0]));
            $this->assertTrue($trieObject->isMember($entry[0]));
        }
    }

    public function testSearch()
    {
        $trieObject = new Trie();

        $testData = $this->getTestData();

        foreach ($testData as $entry) {
            $trieObject->add(...$entry);
        }

        $resultCounter = 0;
        foreach ($trieObject->search('car') as $key => $value) {
            $this->assertContains($key, array_column($testData, 0));
            $this->assertContains($value, array_column($testData, 1));
            ++$resultCounter;
        }
        $this->assertEquals(3, $resultCounter);
    }

    private function getTestData()
    {
        return [
            ['cat', 'n. felix domesticus'],
            ['car', 'n. A motor vehicle'],
            ['cart', 'v. To carry awkwardly'],
            ['cart', 'n. A vehicle for carrying things'],
        ];
    }
}
