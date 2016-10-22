<?php

namespace Phulp\Test;

use Phulp\Collection;
use Phulp\DistFile;
use Phulp\PipeIterate;
use Phulp\Source;
use Symfony\Component\Finder\SplFileInfo;

class DistSource extends TestCase
{
    /**
     * @covers Phulp\Source::__construct
     *
     * @expectedException \UnexpectedValueException
     */
    public function testContructor()
    {
        new Source([]);
    }

    /**
     * @covers Phulp\Source::pipe
     */
    public function testPipe()
    {
        $src = new Source([__DIR__]);

        $test = 0;

        $iterate = new PipeIterate(function () use (& $test) {
            $test++;
        });

        $return = $src->pipe($iterate);

        $this->assertInstanceOf(
            '\Phulp\Source',
            $return
        );

        $this->assertTrue($test > 0);
    }

    /**
     * @covers Phulp\Source::getDistFiles
     */
    public function testGetDistFiles()
    {
        $src = new Source([__DIR__]);

        $this->assertInstanceOf(Collection::class, $src->getDistFiles());
        $this->assertEquals(DistFile::class, $src->getDistFiles()->getType());
    }
}
