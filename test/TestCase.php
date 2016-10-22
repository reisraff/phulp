<?php

namespace Phulp\Test;

use PHPUnit_Framework_TestCase;

abstract class TestCase extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        parent::setUp();

        \Phulp\Output::$quiet = true;
    }
}
