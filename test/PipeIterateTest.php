<?php

namespace Phulp\Test;

use Phulp\Collection;
use Phulp\PipeIterate;
use Phulp\Source;

class PipeIterateTest extends TestCase
{
    /**
     * @covers       Phulp\PipeIterate::execute
     * @dataProvider provideExecuteData
     *
     * @param array $distFiles
     */
    public function testExecute(array $distFiles)
    {
        $i = 0;

        $callback = function () use (& $i) {
            $i++;
        };

        $pipeIterate = new PipeIterate($callback);

        $collection = $this->createMock(Collection::class);
        $collection->method('toArray')
            ->willReturn($distFiles);

        /** @var Source $src */
        $src = $this->createMock(Source::class);
        $src->method('getDistFiles')
            ->willReturn($collection);

        $pipeIterate->execute($src);

        $this->assertEquals(count($distFiles), $i);
    }

    /**
     * @return array[]
     */
    public function provideExecuteData()
    {
        return [
            'noDistFiles' => [
                'distFiles' => [],
            ],
            'withDistFiles' => [
                'distFiles' => [
                    '/path/to/file.php',
                    '/path/to/another/file.php',
                    '/path/to/another/file.php',
                ],
            ],
        ];
    }
}
