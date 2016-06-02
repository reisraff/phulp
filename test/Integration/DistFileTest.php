<?php

namespace Phulp\Test\Integration;

use Phulp\DistFile as Testee;
use Phulp\Test\TestCase;

class DistFileTest extends TestCase
{
    /**
     * @covers Phulp\DistFile::setContent
     */
    public function testSetContent()
    {
        $this->assertSame(
            $content = 'content',
            (new Testee(null))
                ->setContent($content)
                ->getContent()
        );
    }

    /**
     * @covers Phulp\DistFile::setDistpathname
     */
    public function testSetDistpathname()
    {
        $this->assertSame(
            $distpathname = 'distpathname',
            (new Testee(null))
                ->setDistpathname($distpathname)
                ->getDistpathname()
        );
    }

    /**
     * @covers Phulp\DistFile::setLastChangeTime
     */
    public function testSetLastChangeTime()
    {
        $this->assertSame(
            $lastChangeTime = 'lastChangeTime',
            (new Testee(null))
                ->setLastChangeTime($lastChangeTime)
                ->getLastChangeTime()
        );
    }
}
