<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Wod\Collection;

class CollectionTest extends TestCase
{
    public function testCollectionIsEmptyByDefault()
    {
        $collection = new Collection();
        $this->assertEmpty($collection->toArray());
        $this->assertEquals(0, $collection->count());
    }

    public function testCanCreateCollectionFromArray()
    {
        $data       = ['foo', 'bar', 'baz'];
        $collection = new Collection($data);
        $this->assertEquals($data, $collection->toArray());
    }

    public function testCollectionIsIterable()
    {
        $data       = ['foo', 'bar', 'baz'];
        $collection = new Collection($data);

        $counter = 0;
        foreach ($collection as $item) {
            $this->assertSame($data[$counter], $item);
            $counter++;
        }

        $this->assertEquals(3, $counter, "The counter should be same as the number of element in the array");
    }

    public function testCanAddItems()
    {
        $collection = new Collection(['foo', 'bar']);

        $collection->add('baz');
        $this->assertEquals(['foo', 'bar', 'baz'], $collection->toArray());
    }

    public function testCanAddItemByKey()
    {
        $collection = new Collection(['foo', 'bar']);

        $collection->set(0, 'baz');
        $this->assertEquals(['baz', 'bar'], $collection->toArray());

        $collection->set('qux', 'quux');
        $this->assertEquals(['baz', 'bar', 'qux' => 'quux'], $collection->toArray());
    }

    public function testCanGetRandomElement()
    {
        $collection = new Collection([1, 2, 3, 4, 5, 6]);
        $random = $collection->random();
        $this->assertIsInt($random);
        $this->assertContains($random, $collection->toArray());
    }

    public function testCanClearCollection()
    {
        $collection = new Collection([1, 2, 3, 4, 5, 6]);
        $this->assertNotEmpty($collection->toArray());
        $this->assertEquals(6, $collection->count());


        $collection->clear();
        $this->assertEmpty($collection);
        $this->assertEquals(0, $collection->count());
    }

}
