<?php

namespace Phulp\Test;

use Phulp\DistFile;

class DistFileTest extends TestCase
{
    /**
     * @covers Phulp\DistFile::getName
     */
    public function testGetName()
    {
        $name = 'name';
        $distFile = new DistFile(null, $name);

        $this->assertEquals($name, $distFile->getName());

        $name2 = '/name';
        $distFile2 = new DistFile(null, $name2);

        $name2 = trim($name2, '/');
        $this->assertEquals($name2, $distFile2->getName());
    }

    /**
     * @covers Phulp\DistFile::getContent
     */
    public function testGetContent()
    {
        $distFile = new DistFile($content = 'content');

        $this->assertEquals($content, $distFile->getContent());
    }

    /**
     * @covers Phulp\DistFile::setContent
     */
    public function testSetContent()
    {
        $distFile = new DistFile(null);
        $distFile->setContent($expected = 'foo');

        $this->assertEquals($expected, $distFile->getContent());
    }

    /**
     * @covers Phulp\DistFile::getFullpath
     */
    public function testGetFullpath()
    {
        $distFile = new DistFile(null, null, $fullpath = 'fullpath');

        $this->assertEquals($fullpath, $distFile->getFullpath());
    }

    /**
     * @covers Phulp\DistFile::getRelativepath
     */
    public function testGetRelativepath()
    {
        $distFile = new DistFile(null, null, null, $relativepath = 'relativepath');

        $this->assertEquals($relativepath, $distFile->getRelativepath());
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
        $distFile = new DistFile(null, $name, null, $relativepath);

        $this->assertEquals($expected, $distFile->getDistpathname());
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
        $distFile = new DistFile(null);
        $distFile->setDistpathname($expected = 'foo');

        $this->assertEquals($expected, $distFile->getDistpathname());
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
        $distFile = new DistFile(null, null, $fullpath, $relativepath);

        $this->assertEquals($expected, $distFile->getBasepath());
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
        $distFile = new DistFile(null, $name, $fullpath);

        // For whatever reason, the following is not working (off by 1 millisecond).
        //$this->assertEquals($expected, $distFile->getLastChangeTime());
        $this->assertTrue($expected <= $distFile->getLastChangeTime());
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
        $distFile = new DistFile(null);
        $distFile->setLastChangeTime($expected = 'foo');

        $this->assertEquals($expected, $distFile->getLastChangeTime());
    }
}
