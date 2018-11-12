<?php

namespace Tries;

use PHPUnit\Framework\TestCase;

class RadixTrieTest extends TestCase
{
    public function testInstantiate()
    {
        $trieObject = new RadixTrie();
        //    Must return an object...
        $this->assertTrue(is_object($trieObject));
        //    ... of the correct type
        $this->assertTrue(is_a($trieObject, __NAMESPACE__ . '\RadixTrie'));
    }

    public function testAdd()
    {
        $trieObject = new RadixTrie();

        $testData = $this->getTestData();

        foreach ($testData as $entry) {
            $trieObject->add(...$entry);
        }

        // For our test data, 'art' should not be a node
        $this->assertFalse($trieObject->isNode('art'));

        // For our test data, 'car' should be a node without any values
        $this->assertTrue($trieObject->isNode('car'));
        $this->assertFalse($trieObject->isMember('car'));

        foreach ($testData as $entry) {
            $this->assertTrue($trieObject->isNode($entry[0]));
            $this->assertTrue($trieObject->isMember($entry[0]));
        }
    }

    public function testSearch()
    {
        $trieObject = new RadixTrie();

        $testData = $this->getTestData();

        foreach ($testData as $entry) {
            $trieObject->add(...$entry);
        }

        $resultCounter = 0;
        foreach ($trieObject->search('carp') as $key => $value) {
            $this->assertContains($key, array_column($testData, 0));
            $this->assertContains($value, array_column($testData, 1));
            ++$resultCounter;
        }
        $this->assertEquals(4, $resultCounter);
    }

    private function getTestData()
    {
        return [
            ['cart', 'v. To carry awkwardly'],
            ['cart', 'n. A vehicle for carrying things'],
            ['carp', 'v. To complain'],
            ['carp', 'n. A species of fish, not good with chips'],
            ['carpenter', 'n. A person that mends carps'],
            ['carpentry', 'n. Complaints made by a carpenter'],
        ];
    }
}
