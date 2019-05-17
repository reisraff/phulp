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
     * @var array
     */
    private $arguments = [];

    /**
     * @var LoopInterface
     */
    private $loop = null;

    public function __construct(array $arguments = null)
    {
        if (is_array($arguments)) {
            $this->arguments = $arguments;
        }
    }

    /**
     * @param string $task
     */
    public function run(array $tasks)
    {
        $tasks = count($tasks) ? $tasks : ['default'];
        $this->start($tasks);
        $this->getLoop()->run();
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

            Output::out(sprintf(
                '[%s] Starting task "%s' . PHP_EOL,
                Output::colorize((new \DateTime())->format('H:i:s'), 'light_gray'),
                Output::colorize($task, 'light_cyan')
            ));

            $start = microtime(true);
            $callback = $this->tasks[$task];
            $callback($this);

            Output::out(sprintf(
                '[%s] Task "%s" has finished in %s' . PHP_EOL,
                Output::colorize((new \DateTime())->format('H:i:s'), 'light_gray'),
                Output::colorize($task, 'light_cyan'),
                Output::colorize(
                    sprintf(
                        '%s seconds',
                        round(microtime(true) - $start, 4)
                    ),
                    'magenta'
                )
            ));
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
     * @param string $pattern
     *
     * @return Source
     */
    public function src(array $pattern)
    {
        return new Source($pattern);
    }

    /**
     * @param Source $src
     * @param callable $callback
     *
     * @return Watch
     */
    public function watch(Source $src, callable $callback)
    {
        return new Watch($src, $callback, $this);
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
        $this->loop = $this->loop ?: Factory::create();

        return $this->loop;
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

    /**
     * Execute an external command
     *
     * @param string
     * @param array options
     *
     * @return Command
     */
    public function exec($command, array $options = [])
    {
        return new Command($command, $options, $this->getLoop());
    }

    /**
     * Gets the value of arguments.
     *
     * @return array
     */
    public function getArguments()
    {
        return $this->arguments;
    }

    /**
     * Gets the value of arguments.
     *
     * @param $name the argument name
     * @param $default the default return value if the argument does not exists
     *
     * @return array
     */
    public function getArgument($name, $default = null)
    {
        return isset($this->arguments[$name]) ? $this->arguments[$name] : $default;
    }
}
