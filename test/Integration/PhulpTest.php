<?php

namespace Phulp\Test\Integration;

use Phulp\Phulp as Testee;
use Phulp\Test\TestCase;

class PhulpTest extends TestCase
{
    /**
     * @covers Phulp::run
     * @covers Phulp::task
     */
    public function testRun()
    {
        $test = 0;

        $phulp = new Testee;
        $phulp->task(
            'test',
            function ($phulp) use (& $test) {
                $test++;
            }
        );
        $phulp->run('test');

        $this->assertSame(1, $test);
    }

    /**
     * @covers Phulp::start
     * @covers Phulp::task
     */
    public function testStart()
    {
        $test = 0;

        $phulp = new Testee;
        $phulp->task(
            'test',
            function ($phulp) use (& $test) {
                $test++;
            }
        );
        $phulp->start(['test']);

        $this->assertSame(1, $test);
    }
}
