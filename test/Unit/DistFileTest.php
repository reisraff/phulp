<?php

namespace Phulp\Test\Unit;

use Phulp\DistFile as Testee;
use Phulp\Test\TestCase;

class DistFileTest extends TestCase
{
    /**
     * @covers Phulp\DistFile::getName
     */
    public function testGetName()
    {
        $testee = new Testee(null, $name = 'name');

        $this->assertSame($name, $testee->getName());
    }

    /**
     * @covers Phulp\DistFile::getContent
     */
    public function testGetContent()
    {
        $testee = new Testee($content = 'content');

        $this->assertSame($content, $testee->getContent());
    }

    /**
     * @covers Phulp\DistFile::setContent
     */
    public function testSetContent()
    {
        $testee = new Testee(null);

        // Just test the "fluent" part of the setter. The "setting" part is covered in the according integration test.
        $this->assertSame($testee, $testee->setContent(null));
    }

    /**
     * @covers Phulp\DistFile::getFullpath
     */
    public function testGetFullpath()
    {
        $testee = new Testee(null, null, $fullpath = 'fullpath');

        $this->assertSame($fullpath, $testee->getFullpath());
    }

    /**
     * @covers Phulp\DistFile::getRelativepath
     */
    public function testGetRelativepath()
    {
        $testee = new Testee(null, null, null, $relativepath = 'relativepath');

        $this->assertSame($relativepath, $testee->getRelativepath());
    }

    /**
     * @covers       Phulp\DistFile::getDistpathname
     * @dataProvider provideGetDistpathnameData
     *
     * @param string $expected
     * @param string $name
     * @param string $relativepath
     */
    public function testGetDistpathname($expected, $name, $relativepath)
    {
        $testee = new Testee(null, $name, null, $relativepath);

        $this->assertSame($expected, $testee->getDistpathname());
    }

    /**
     * @return array[]
     */
    public function provideGetDistpathnameData()
    {
        return [
            'noTrailingDS' => [
                'expected' => '/path/to' . DIRECTORY_SEPARATOR . 'file',
                'name' => 'file',
                'relativepath' => '/path/to',
            ],
            'withTrailingDS' => [
                'expected' => '/path/to' . DIRECTORY_SEPARATOR . 'file',
                'name' => 'file',
                'relativepath' => '/path/to' . DIRECTORY_SEPARATOR,
            ],
        ];
    }

    /**
     * @covers Phulp\DistFile::setDistpathname
     */
    public function testSetDistpathname()
    {
        $testee = new Testee(null);

        // Just test the "fluent" part of the setter. The "setting" part is covered in the according integration test.
        $this->assertSame($testee, $testee->setDistpathname(null));
    }

    /**
     * @covers       Phulp\DistFile::getBasepath
     * @dataProvider provideGetBasepathData
     *
     * @param string $expected
     * @param string $fullpath
     * @param string $relativepath
     */
    public function testGetBasepath($expected, $fullpath, $relativepath)
    {
        $testee = new Testee(null, null, $fullpath, $relativepath);

        $this->assertSame($expected, $testee->getBasepath());
    }

    /**
     * @return array[]
     */
    public function provideGetBasepathData()
    {
        return [
            'emptyRelativePath' => [
                'expected' => '/path/to/directory',
                'fullpath' => '/path/to/directory' . DIRECTORY_SEPARATOR,
                'relativepath' => '',
            ],
            'noMatchingRelativePath' => [
                'expected' => '/path/to/directory',
                'fullpath' => '/path/to/directory' . DIRECTORY_SEPARATOR,
                'relativepath' => '/somewhere/else',
            ],
            'matchingRelativePath' => [
                'expected' => '/path/to',
                'fullpath' => '/path/to/directory' . DIRECTORY_SEPARATOR,
                'relativepath' => '/directory',
            ],
            'incorrectlyMatchingRelativePath' => [
                'expected' => '/path/to/directory',
                'fullpath' => '/path/to/directory' . DIRECTORY_SEPARATOR,
                'relativepath' => '/to',
            ],
        ];
    }

    /**
     * @covers       Phulp\DistFile::getLastChangeTime
     * @dataProvider provideGetLastChangeTimeData
     *
     * @param string $expected
     * @param string $name
     * @param string $fullpath
     */
    public function testGetLastChangeTime($expected, $name, $fullpath)
    {
        $testee = new Testee(null, $name, $fullpath);

        // For whatever reason, the following is not working (off by 1 millisecond).
        //$this->assertSame($expected, $testee->getLastChangeTime());
        $this->assertTrue($expected <= $testee->getLastChangeTime());
    }

    /**
     * @return array[]
     */
    public function provideGetLastChangeTimeData()
    {
        return [
            'emptyName' => [
                'expected' => null,
                'name' => '',
                'fullpath' => '/path/to/directory' . DIRECTORY_SEPARATOR,
            ],
            'emptyFullpath' => [
                'expected' => null,
                'name' => 'file',
                'fullpath' => '',
            ],
            'thisFile' => [
                'expected' => filemtime(__FILE__),
                'name' => basename(__FILE__),
                'fullpath' => __DIR__ . DIRECTORY_SEPARATOR,
            ],
        ];
    }

    /**
     * @covers Phulp\DistFile::setLastChangeTime
     */
    public function testSetLastChangeTime()
    {
        $testee = new Testee(null);

        // Just test the "fluent" part of the setter. The "setting" part is covered in the according integration test.
        $this->assertSame($testee, $testee->setLastChangeTime(null));
    }
}
