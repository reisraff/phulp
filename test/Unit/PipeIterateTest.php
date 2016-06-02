<?php

namespace Phulp\Test\Unit;

use Brain\Monkey;
use Mockery;
use Phulp\PipeIterate as Testee;
use Phulp\Source;
use Phulp\Test\TestCase;

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
        Monkey\Functions::expect($callback = 'callback')
            ->times(count($distFiles));

        $testee = new Testee($callback);

        /** @var Source $src */
        $src = Mockery::mock(Source::class)
            ->shouldReceive('getDistFiles')
            ->andReturn($distFiles)
            ->getMock();
        $testee->execute($src);
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
