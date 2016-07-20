<?php

namespace Phulp\Test;

use Brain\Monkey;
use Mockery;
use PHPUnit_Framework_TestCase;

abstract class TestCase extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        parent::setUp();
        Monkey::setUp();

        \Phulp\Output::$quiet = true;
    }

    protected function tearDown()
    {
        Monkey::tearDown();
        Mockery::close();
        parent::tearDown();
    }
}
