<?php

namespace Phulp;

use React\ChildProcess\Process;
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
                '[%s] Starting task "%s"',
                Output::colorize((new \DateTime())->format('H:i:s'), 'light_gray'),
                Output::colorize($task, 'light_cyan')
            ));

            $start = microtime(true);
            $callback = $this->tasks[$task];
            $callback($this);

            Output::out(sprintf(
                '[%s] Task "%s" has finished in %s',
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
     * @param array $command example ['command' => 'echo 1', 'env' => ['FOO' => 'BAR'], 'cwd' => '/tmp']
     * @param bool $async default false
     * @param callable $callback called when the async commans is terminated $callback($exitCode, $output)
     *
     * @return bool|array false when command fails, true when async is thrown,
     * array ['exit_code' => 0, 'output' => '1'] when sync command ends
     *
     * @throws \RuntimeException
     */
    public function exec(array $command, $async = false, callable $callback = null)
    {
        if (! array_key_exists('command', $command)) {
            throw new \RuntimeException('command[command] is required');
        }

        $defaults = [
            'env' => null,
            'cwd' => getcwd(),
        ];

        $command = array_merge($defaults, $command);

        if ($async) {
            $process = new Process($command['command'], $command['cwd'], $command['env']);
            $process->start($this->getLoop());

            $output = null;

            $process->stdout->on('data', function ($data) use (&$output) {
                $data = rtrim($data, PHP_EOL);
                $output .= $data . PHP_EOL;
                Output::out($data);
            });

            $process->stdout->on('error', function ($data) use (&$output) {
                $data = rtrim($data, PHP_EOL);
                $output .= $data . PHP_EOL;
                Output::out($data);
            });

            $process->on('exit', function($exitCode, $termSignal) use ($callback, &$output) {
                if ($callback) {
                    $callback($exitCode, $output);
                }
            });

            return true;
        }

        $descriptorspec = [
           0 => ['pipe', 'r'], // stdin is a pipe that the child will read from
           1 => ['pipe', 'w'], // stdout is a pipe that the child will write to
           2 => ['pipe', 'w'], // stderr is a pipe that the child will write to
        ];

        $process = proc_open(
            $command['command'],
            $descriptorspec,
            $pipes,
            $command['cwd'],
            $command['env']
        );

        $output = null;

        if (is_resource($process)) {
            fclose($pipes[0]);

            while ($data = fgets($pipes[1])) {
                $data = rtrim($data, PHP_EOL);
                $output .= $data . PHP_EOL;
                Output::out($data);
            }
            fclose($pipes[1]);

            while ($data = fgets($pipes[2])) {
                $data = rtrim($data, PHP_EOL);
                $output .= $data . PHP_EOL;
                Output::out($data);
            }
            fclose($pipes[2]);

            return [
                'exit_code' => proc_close($process),
                'output' => trim($output),
            ];
        }

        return false;
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
