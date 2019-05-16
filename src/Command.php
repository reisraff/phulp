<?php

namespace Phulp;

use React\ChildProcess\Process;
use React\EventLoop\LoopInterface;

class Command
{
    /**
     * @var string
     */
    protected $command = null;

    /**
     * @var array
     */
    protected $options = null;

    /**
     * @var LoopInterface
     */
    protected $loop = null;

    /**
     * @var resource
     */
    protected $syncStdIn = null;

    /**
     * @var array
     */
    protected $chunck = [
        /** @var string */
        'out' => null,
        /** @var string */
        'err' => null,
    ];

    /**
     * @var string
     */
    protected $stdout = null;

    /**
     * @var string
     */
    protected $stderr = null;

    /**
     * @var int
     */
    protected $exitCode = null;

    /**
     * @var string
     */
    // protected $lastChunck = null;

    /**
     * @var int
     */
    // protected $lastChunckNullCounter = 0;

    /**
     * @param string $command
     * @param array $options
     */
    public function __construct($command, array $options, LoopInterface $loop)
    {
        $this->command = $command;

        $this->options = array_merge([
            'cwd' => getcwd(),
            'env' => [],
            'quiet' => false,
            'sync' => true,
            'onStdOut' => null,
            'onStdErr' => null,
            'onFinish' => null,
            // 'onInteractivity' => null,
        ], $options);

        $this->loop = $loop;

        $this->start((bool) $this->options['sync']);
    }

    /**
     * @var bool $sync
     */
    protected function start($sync)
    {
        if ($sync) {
            $descriptorspec = [
               0 => ['pipe', 'r'], // stdin is a pipe that the child will read from
               1 => ['pipe', 'w'], // stdout is a pipe that the child will write to
               2 => ['pipe', 'w'], // stderr is a pipe that the child will write to
            ];

            $this->process = proc_open(
                $this->command,
                $descriptorspec,
                $pipes,
                $this->options['cwd'],
                $this->options['env']
            );

            if (is_resource($this->process)) {
                $this->syncStdIn = $pipes[0];

                while ($data = stream_get_contents($pipes[1], 65536)) {
                    if ('' !== $data) {
                        $this->tick($data, 'out');
                    }
                }
                fclose($pipes[1]);

                while ($data = stream_get_contents($pipes[2], 65536)) {
                    if ('' !== $data) {
                        $this->tick($data, 'err');
                    }
                }
                fclose($pipes[2]);

                fclose($pipes[0]);
                $this->exitCode = proc_close($this->process);
            }
        } else {
            $this->process = new Process($this->command, $this->options['cwd'], $this->options['env']);
            $this->process->start($this->loop);

            $command = $this;

            $this->process->stdout->on('data', function ($data) use ($command) {
                $command->tick($data, 'out');
            });

            $this->process->stderr->on('data', function ($data) use ($command) {
                $command->tick($data, 'err');
            });

            // $this->checkInteractivity = $this->loop->addPeriodicTimer(0.001, function () use ($command) {
            //     $command->checkInteractivity();
            // });

            $this->process->on('exit', function ($exitCode, $termSignal) use ($command) {
                $onFinish = $command->options['onFinish'];
                if (is_callable($onFinish)) {
                    $command->exitCode = $exitCode;
                    $onFinish($command->exitCode, $command->stdout, $command->stderr);
                }

                // if ($this->checkInteractivity) {
                //     $this->checkInteractivity->cancel();
                // }
            });
        }
    }

    /**
     * @var string $data
     */
    public function write($data)
    {
        if (! $this->options['quiet']) {
            Output::out($data);
        }

        if ($this->options['sync']) {
            fwrite($this->syncStdIn, $data . PHP_EOL);
        } else {
            $this->process->stdin->write($data);
            $this->process->stdin->end($data = null);
        }
    }

    // protected function checkInteractivity()
    // {
    //     $callback = $this->options['onInteractivity'];
    //     $call = function (& $chunck) use ($callback) {
    //         if (is_callable($callback)) {
    //             $callback($chunck, $this);
    //             $chunck = null;
    //         }
    //     };

    //     if (null === $this->lastChunck) {
    //         $this->lastChunck = $this->chunck['out'];
    //         if (10 == ++$this->lastChunckNullCounter) {
    //             $call($this->lastChunck);
    //             $this->lastChunckNullCounter = 0;
    //         }
    //         return;
    //     }

    //     if ($this->lastChunck === $this->chunck['out']) {
    //         $call($this->lastChunck);
    //     }
    // }

    /**
     * @param string $data
     * @param string $std
     */
    protected function tick($data, $std)
    {
        $this->{sprintf('std%s', $std)} .= $data;
        $this->chunck[$std] .= $data;

        $end = PHP_EOL === $this->chunck[$std][strlen($this->chunck[$std]) - 1];

        $lines = explode(PHP_EOL, $this->chunck[$std]);
        $this->chunck[$std] = array_pop($lines);

        $stdCallback = $this->options[sprintf('onStd%s', ucfirst($std))];
        $out = function ($line) use ($stdCallback, $std) {
            if (is_callable($stdCallback)) {
                $stdCallback($line);
            }

            if (! $this->options['quiet']) {
                if (trim($line) != "") {
                    Output::{$std}($line . PHP_EOL);
                }
            }
        };

        foreach ($lines as $line) {
            $out($line);
        }

        if ($end) {
            $out($this->chunck[$std]);
            $this->chunck[$std] = null;
        }
    }

    /**
     * Gets the value of exitCode.
     *
     * @return int
     */
    public function getExitCode()
    {
        return $this->exitCode;
    }

    /**
     * Gets the value of stdout.
     *
     * @return string
     */
    public function getStdOut()
    {
        return $this->stdout;
    }

    /**
     * Gets the value of stderr.
     *
     * @return string
     */
    public function getStdErr()
    {
        return $this->stderr;
    }
}
