<?php

namespace Phulp;

class Phulp
{
    /**
     * @var array
     */
    private static $tasks = [];

    /**
     * @param string $task
     */
    final public static function run($task = null)
    {
        try {
            $start = microtime(true);
            Output::out('Starting the script', 'blue');
            Phulp::start([(!empty($task) ? $task : 'default')]);
            Output::out('Script has finished in ' . round(microtime(true) - $start, 4) . ' seconds', 'blue');
        } catch (\Exception $e) {
            Output::out($e->getMessage(), 'red');
        }
    }

    /**
     * @param array $tasks
     *
     * @throws \Exception
     */
    final public static function start(array $tasks)
    {
        foreach ($tasks as $task) {
            if (!isset(self::$tasks[$task])) {
                // @todo improve it
                throw new \Exception('The task "' . $task . '" does not exists.');
            }

            Output::out('Executing "' . $task . '"', 'green');
            $start = microtime(true);
            self::$tasks[$task]();
            Output::out(
                'Task "' . $task . '" has finished in ' . round(microtime(true) - $start, 4) . ' seconds',
                'green'
            );
        }
    }

    /**
     * @todo add the task's dependencies
     *
     * @param string $name
     * @param callable $callback
     */
    final public static function task($name, callable $callback)
    {
        self::$tasks[$name] = $callback;
    }

    /**
     * @param array $dirs
     * @param string $pattern
     * @param boolean $recursive
     *
     * @return Source
     */
    final public static function src(array $dirs, $pattern = null, $recursive = true)
    {
        return new Source($dirs, $pattern, $recursive);
    }

    /**
     * @param Source $src
     * @param array $tasks
     */
    final public static function watch(Source $src, array $tasks)
    {
        new Watch($src, $tasks);
    }

    /**
     * @param string $path
     *
     * @return PipeInterface
     */
    final public static function dest($path)
    {
        return self::iterate(function ($distFile) use ($path) {
            $filename = $distFile->getDistpathname();
            $relativepath = null;

            if (strrpos($filename, DIRECTORY_SEPARATOR)) {
                $filename = substr(
                    $distFile->getDistpathname(),
                    strrpos($distFile->getDistpathname(), DIRECTORY_SEPARATOR) + 1
                );

                $relativepath = substr(
                    $distFile->getDistpathname(),
                    0,
                    strrpos($distFile->getDistpathname(), DIRECTORY_SEPARATOR)
                );
            }

            $dir = $path . DIRECTORY_SEPARATOR . $relativepath;
            if (!empty($relativepath) && !file_exists($dir)) {
                mkdir($dir, 0777, true);
            }

            file_put_contents(
                $path . DIRECTORY_SEPARATOR . $distFile->getDistpathname(),
                $distFile->getContent()
            );
        });
    }

    /**
     * @return PipeInterface
     */
    final public static function clean()
    {
        return self::iterate(function ($distFile) {
            $file = rtrim($distFile->getFullpath(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $distFile->getName();

            if (file_exists($file)) {
                @unlink($file);

                $currentDir = substr($file, 0, strrpos($file, DIRECTORY_SEPARATOR));

                while ($distFile->getBasepath() != $currentDir) {
                    @rmdir($currentDir);

                    $currentDir = substr($currentDir, 0, strrpos($currentDir, DIRECTORY_SEPARATOR));
                }
            }
        });
    }

    /**
     * @param callable $callback
     */
    final public static function iterate(callable $callback)
    {
        return new PipeIterate($callback);
    }
}
