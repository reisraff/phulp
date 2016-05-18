<?php

namespace Phulp;

abstract class Phulp implements PhulpInterface
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
            Phulp::start([(!empty($task) ? $task : 'default')]);
        } catch (\Exception $e) {
            // @todo improve it
            die($e->getMessage());
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
            if (isset(self::$tasks[$task])) {
                self::$tasks[$task]();
            } else {
                // @todo improve it
                throw new \Exception('The task "' . $task . '" does not exists.' . PHP_EOL);
            }
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

            if (!empty($relativepath)) {
                $dir = $path . DIRECTORY_SEPARATOR . $relativepath;
                if (!file_exists($dir)) {
                    mkdir($dir, 0777, true);
                }
            }

            file_put_contents(
                $path . DIRECTORY_SEPARATOR . $distFile->getDistpathname(),
                $distFile->getContent()
            );
        });
    }

    /**
     * @todo improve it :'(
     *
     * @return PipeInterface
     */
    final public static function clean()
    {
        return self::iterate(function ($distFile) {
            if (file_exists($distFile->getBasepath()) && is_dir($distFile->getBasepath())) {
                exec('rm -rf ' . $distFile->getBasepath() . DIRECTORY_SEPARATOR . '*');
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
