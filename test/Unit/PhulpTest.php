<?php

namespace Phulp\Test\Unit;

use Phulp\Phulp as Testee;
use Phulp\Test\TestCase;

class PhulpTest extends TestCase
{
    /**
     * @covers Phulp::src
     */
    public function testSrc()
    {
        $this->assertInstanceOf(
            '\Phulp\Source',
            (new Testee)->src([__DIR__])
        );
    }

    /**
     * @covers Phulp::dest
     */
    public function testDest()
    {
        $this->assertInstanceOf(
            '\Phulp\PipeIterate',
            (new Testee)->dest(__DIR__)
        );
    }

    /**
     * @covers Phulp::clean
     */
    public function testClean()
    {
        $this->assertInstanceOf(
            '\Phulp\PipeIterate',
            (new Testee)->clean()
        );
    }

    /**
     * @covers Phulp::iterate
     */
    public function testIterate()
    {
        $this->assertInstanceOf(
            '\Phulp\PipeIterate',
            (new Testee)->iterate(
                function () {
                }
            )
        );
    }
}
