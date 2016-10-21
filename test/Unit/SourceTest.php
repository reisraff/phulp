<?php

namespace Phulp\Test\Unit;

use Phulp\Source as Testee;
use Phulp\DistFile;
use Phulp\Collection;
use Phulp\PipeIterate;
use Phulp\Test\TestCase;
use Symfony\Component\Finder\SplFileInfo;

class DistSource extends TestCase
{
    /**
     * @covers Phulp\Source::pipe
     */
    public function testPipe()
    {
        $src = new Testee([__DIR__]);

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
        $src = new Testee([__DIR__]);

        $this->assertInstanceOf(Collection::class, $src->getDistFiles());
        $this->assertEquals(DistFile::class, $src->getDistFiles()->getType());
    }

    /**
     * @covers Phulp\Source::getDirs
     */
    public function testGetDirs()
    {
        $src = new Testee([__DIR__]);

        $this->assertInstanceOf(Collection::class, $src->getDirs());
        $this->assertEquals(SplFileInfo::class, $src->getDirs()->getType());
    }
}
