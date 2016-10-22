<?php

namespace Phulp\Test\Dummie;

use React\EventLoop\LoopInterface;

class TestLopp implements LoopInterface
{
    public function addReadStream($stream, callable $listener)
    {
    }

    public function addWriteStream($stream, callable $listener)
    {
    }

    public function removeReadStream($stream)
    {
    }

    public function removeWriteStream($stream)
    {
    }

    public function removeStream($stream)
    {
    }

    public function addTimer($interval, callable $callback)
    {
    }

    public function addPeriodicTimer($interval, callable $callback)
    {
    }

    public function cancelTimer(\React\EventLoop\Timer\TimerInterface $timer)
    {
    }

    public function isTimerActive(\React\EventLoop\Timer\TimerInterface $timer)
    {
    }

    public function nextTick(callable $listener)
    {
    }

    public function futureTick(callable $listener)
    {
    }

    public function tick()
    {
    }

    public function run()
    {
    }

    public function stop()
    {
    }
}
