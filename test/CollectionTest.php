<?php

namespace Phulp\Test;

use Phulp\Collection;

class CollectionTest extends TestCase
{
    /**
     * @covers Phulp\Collection::__construct
     */
    public function testConsistencyOfTypes()
    {
        $expect = [1,2,3];

        $this->assertEquals($expect, (new Collection($expect))->toArray());
    }

    /**
     * @covers Phulp\Collection::__construct
     * @covers Phulp\Collection::getType
     */
    public function testStaticTypeSet()
    {
        $expect = 'foo';

        $this->assertEquals($expect, (new Collection([], $expect))->getType());
    }

    /**
     * @covers Phulp\Collection::__construct
     * @covers Phulp\Collection::getType
     */
    public function testDinamicTypeSet()
    {
        $items = [1,2,3];

        $this->assertEquals(gettype($items[0]), (new Collection($items))->getType());
    }
    /**
     * @covers Phulp\Collection::__construct
     * @covers Phulp\Collection::add
     */
    public function testAdd()
    {
        $items = [1,2,3];
        $add = 1;

        $collection = new Collection($items);
        $collection->add($add);

        $this->assertEquals(
            [1,2,3,1],
            $collection->toArray()
        );
    }
    /**
     * @covers Phulp\Collection::__construct
     * @covers Phulp\Collection::set
     */
    public function testSet()
    {
        $items = [1,2,3,3,5,6,7,8,9,0];

        $collection = new Collection($items);
        $collection->set(3, 4);
        $this->assertEquals(
            [1,2,3,4,5,6,7,8,9,0],
            $collection->toArray()
        );
    }

    /**
     * @covers Phulp\Collection::__construct
     *
     * @expectedException \UnexpectedValueException
     */
    public function testConstructException()
    {
        $items = [1,'3'];

        new Collection($items);
    }

    /**
     * @covers Phulp\Collection::__construct
     *
     * @expectedException \UnexpectedValueException
     */
    public function testConstructExceptionWithStaticTypeSet()
    {
        $items = [1,2];

        new Collection($items, 'foo');
    }

    /**
     * @covers Phulp\Collection::add
     *
     * @expectedException \UnexpectedValueException
     */
    public function testAddException()
    {
        $items = [1,2];

        (new Collection($items))->add('foo');
    }

    /**
     * @covers Phulp\Collection::set
     *
     * @expectedException \UnexpectedValueException
     */
    public function testSetException()
    {
        $items = [1,2];

        (new Collection($items))->set(1, 'foo');
    }
}
