<?php

namespace Phulp;

use React\EventLoop\LoopInterface;
use React\EventLoop\Factory;

class Phulp
{
    /**
     * @var array
     */
    private $tasks = [];

    /**
     * @var LoopInterface
     */
    private $loop = null;

    /**
     * @param string $task
     */
    public function run($task = null)
    {
        try {
            $this->start([(!empty($task) ? $task : 'default')]);
            $this->getLoop()->run();
        } catch (\Exception $e) {
            Output::err(
                '[' . Output::colorize((new \DateTime())->format('H:i:s'), 'light_gray') . ']'
                . ' ' . Output::colorize($e->getMessage(), 'light_red')
            );
            exit(1);
        }
    }

    /**
     * @param array $tasks
     *
     * @throws \RuntimeException
     */
    public function start(array $tasks)
    {
        foreach ($tasks as $task) {
            if (!isset($this->tasks[$task])) {
                throw new \RuntimeException('The task "' . $task . '" does not exists.');
            }

            Output::out(
                '[' . Output::colorize((new \DateTime())->format('H:i:s'), 'light_gray') . ']'
                . ' Starting task "' . Output::colorize($task, 'light_cyan') . '"'
            );

            $start = microtime(true);
            $callback = $this->tasks[$task];
            $callback($this);

            Output::out(
                '[' . Output::colorize((new \DateTime())->format('H:i:s'), 'light_gray') . ']'
                . ' Task "' . Output::colorize($task, 'light_cyan') . '" has finished in '
                . Output::colorize(round(microtime(true) - $start, 4) . ' seconds', 'magenta')
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
     * @param mixed $tasks
     *
     * @return Watch
     *
     * @throws \InvalidArgumentException
     */
    public function watch(Source $src, $tasks)
    {
        if (! is_array($tasks) && ! is_callable($tasks)) {
            throw new \InvalidArgumentException('Invalid Argument for Phulp::watch');
        }

        if (is_array($tasks)) {
            Output::out(
                '[' . Output::colorize((new \DateTime())->format('H:i:s'), 'light_gray') . '] '
                . Output::colorize(
                    'the API of Phulp::watch you are using will be deprecated',
                    'yellow'
                )
            );
            Output::out(
                '[' . Output::colorize((new \DateTime())->format('H:i:s'), 'light_gray') . '] '
                .  Output::colorize(
                    'Please check the documentation. ',
                    'yellow'
                )
            );
        }

        return new Watch($src, $tasks, $this);
    }

    /**
     * @param string $path
     *
     * @return PipeInterface
     */
    public function dest($path)
    {
        return $this->iterate(function ($distFile) use ($path) {
            $dir = $path;

            /** @var DistFile $distFile */
            $filename = $distFile->getDistpathname();
            $dsPos = strrpos($filename, DIRECTORY_SEPARATOR);

            if ($dsPos) {
                $dir .= DIRECTORY_SEPARATOR . substr($filename, 0, $dsPos);
            }

            if (!file_exists($dir)) {
                mkdir($dir, 0777, true);
            }

            file_put_contents(
                $path . DIRECTORY_SEPARATOR . $filename,
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

    /**
     * Gets the value of loop.
     *
     * @return LoopInterface
     */
    public function getLoop()
    {
        return $this->loop ?: Factory::create();
    }

    /**
     * Sets the value of loop.
     *
     * @param LoopInterface $loop the loop
     */
    public function setLoop(LoopInterface $loop)
    {
        if (! $this->loop) {
            $this->loop = $loop;
        }
    }
}
