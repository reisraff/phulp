<?php

namespace Phulp\Test;

use Phulp\Phulp;
use Phulp\Source;
use React\EventLoop\LoopInterface;
use Phulp\Test\Dummie\TestLopp;

class PhulpTest extends TestCase
{
    /**
     * @covers Phulp::run
     */
    public function testRunExpectedMethods()
    {
        $loop = $this->createMock(LoopInterface::class);
        $loop->expects($this->once())
            ->method('run');

        $phulp = $this->getMockBuilder(Phulp::class)
            ->setMethods(['start', 'getLoop'])
            ->getMock();

        $phulp->expects($this->once())
            ->method('start')
            ->with(['default']);

        $phulp->expects($this->once())
            ->method('getLoop')
            ->willReturn($loop);

        $phulp->run();
    }

    /**
     * @covers Phulp::run
     */
    public function testRunExpectedMethodsAndTask()
    {
        $loop = $this->createMock(LoopInterface::class);
        $loop->expects($this->once())
            ->method('run');

        $phulp = $this->getMockBuilder(Phulp::class)
            ->setMethods(['start', 'getLoop'])
            ->getMock();

        $phulp->expects($this->once())
            ->method('start')
            ->with(['task']);

        $phulp->expects($this->once())
            ->method('getLoop')
            ->willReturn($loop);

        $phulp->run('task');
    }

    /**
     * @covers Phulp::src
     */
    public function testSrc()
    {
        $this->assertInstanceOf(
            '\Phulp\Source',
            (new Phulp)->src([__DIR__])
        );
    }

    /**
     * @covers Phulp::dest
     */
    public function testDest()
    {
        $this->assertInstanceOf(
            '\Phulp\PipeIterate',
            (new Phulp)->dest(__DIR__)
        );
    }

    /**
     * @covers Phulp::clean
     */
    public function testClean()
    {
        $this->assertInstanceOf(
            '\Phulp\PipeIterate',
            (new Phulp)->clean()
        );
    }

    /**
     * @covers Phulp::getLoop
     */
    public function testGetLoop()
    {
        $this->assertInstanceOf(
            '\React\EventLoop\StreamSelectLoop',
            (new Phulp)->getLoop()
        );
    }

    /**
     * @covers Phulp::setLoop
     */
    public function testSetLoop()
    {
        $phulp = (new Phulp);
        $phulp->setLoop($this->createMock(TestLopp::class));

        $this->assertInstanceOf(
            TestLopp::class,
            $phulp->getLoop()
        );
    }

    /**
     * @covers Phulp::iterate
     */
    public function testIterate()
    {
        $this->assertInstanceOf(
            '\Phulp\PipeIterate',
            (new Phulp)->iterate(
                function () {
                }
            )
        );
    }

    /**
     * @covers Phulp::watch
     * @dataProvider provideWatchValidArgument
     *
     * @param mixed $value
     */
    public function testWatchValidArgument($value)
    {
        $this->assertInstanceOf(
            '\Phulp\Watch',
            (new Phulp)->watch(
                new Source([__DIR__]),
                $value
            )
        );
    }

    /**
     * @return array[]
     */
    public function provideWatchValidArgument()
    {
        return [
            'callback' => [
                'value' => function () {
                },
            ],
            'array' => [
                'value' => [],
            ],
        ];
    }

    /**
     * @covers Phulp::watch
     * @dataProvider provideWatch
     *
     * @param mixed $value
     *
     * @expectedException \InvalidArgumentException
     */
    public function testWatchInvalidArgument($value)
    {
        (new Phulp)->watch(
            new Source([__DIR__]),
            $value
        );
    }

    /**
     * @return array[]
     */
    public function provideWatch()
    {
        return [
            'integer' => [
                'value' => 1,
            ],
            'string' => [
                'value' => 'string',
            ],
            'float' => [
                'value' => 1.8,
            ],
            'object' => [
                'value' => new \StdClass,
            ],
            'boolean' => [
                'value' => false,
            ],
            'null' => [
                'value' => null,
            ],
        ];
    }
    /**
     * @covers Phulp::run
     * @covers Phulp::task
     */
    public function testRunTask()
    {
        $test = 0;

        $phulp = new Phulp;
        $phulp->task(
            'test',
            function ($phulp) use (& $test) {
                $test++;
            }
        );
        $phulp->run('test');

        $this->assertEquals(1, $test);
    }

    /**
     * @covers Phulp::start
     * @covers Phulp::task
     */
    public function testStart()
    {
        $test = 0;

        $phulp = new Phulp;
        $phulp->task(
            'test',
            function ($phulp) use (& $test) {
                $test++;
            }
        );
        $phulp->start(['test']);

        $this->assertEquals(1, $test);
    }

    /**
     * @covers Phulp::start
     *
     * @expectedException \RuntimeException
     */
    public function testStartUndefinedTask()
    {
        $test = 0;

        $phulp = new Phulp;
        $phulp->start(['test']);
    }
}
