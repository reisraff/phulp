<?php

namespace Phulp;

class Phulp
{
    /**
     * @var array
     */
    private $tasks = [];

    /**
     * @param string $task
     */
    public function run($task = null)
    {
        try {
            $start = microtime(true);
            Output::out('Starting the script', 'blue');
            $this->start([(!empty($task) ? $task : 'default')]);
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
    public function start(array $tasks)
    {
        foreach ($tasks as $task) {
            if (!isset($this->tasks[$task])) {
                // @todo improve it
                throw new \Exception('The task "' . $task . '" does not exists.');
            }

            Output::out('Executing "' . $task . '"', 'green');
            $start = microtime(true);
            $this->tasks[$task]($this);
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
    public function task($name, callable $callback)
    {
        $this->tasks[$name] = $callback;
    }

    /**
     * @param array $dirs
     * @param string $pattern
     * @param boolean $recursive
     *
     * @return Source
     */
    public function src(array $dirs, $pattern = null, $recursive = true)
    {
        return new Source($dirs, $pattern, $recursive);
    }

    /**
     * @param Source $src
     * @param array $tasks
     */
    public function watch(Source $src, array $tasks)
    {
        $phulp = $this;
        new Watch($src, function () use ($phulp, $tasks) {
            $phulp->start($tasks);
        });
    }

    /**
     * @param string $path
     *
     * @return PipeInterface
     */
    public function dest($path)
    {
        return $this->iterate(function ($distFile) use ($path) {
            /** @var DistFile $distFile */
            $filename = $distFile->getDistpathname();
            $relativepath = null;

            if (strrpos($filename, DIRECTORY_SEPARATOR)) {
                // TODO: Either remove this (as it is not used), or use it. :)
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
    public function clean()
    {
        return $this->iterate(function ($distFile) {
            /** @var DistFile $distFile */
            $file = rtrim($distFile->getFullpath(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $distFile->getName();

            if (file_exists($file)) {
                @unlink($file);

                $currentDir = substr($file, 0, strrpos($file, DIRECTORY_SEPARATOR));

                while ($distFile->getBasepath() !== $currentDir) {
                    @rmdir($currentDir);

                    $currentDir = substr($currentDir, 0, strrpos($currentDir, DIRECTORY_SEPARATOR));
                }
            }
        });
    }

    /**
     * @param callable $callback
     *
     * @return PipeInterface
     */
    public function iterate(callable $callback)
    {
        return new PipeIterate($callback);
    }
}
