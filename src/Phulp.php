<?php

namespace Phulp;

abstract class Phulp
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
            $task = !empty($task) ? $task : 'default';

            $this->start([$task]);
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
    protected function start(array $tasks)
    {
        foreach ($tasks as $task) {
            if (isset($this->tasks[$task])) {
                $this->tasks[$task]();
            } else {
                // @todo improve it
                throw new \Exception('The task "' . $task . '" does not exists.' . PHP_EOL);
            }
        }
    }

    /**
     * @param string $name
     * @param callable $callback
     *
     * @return self
     */
    protected function task($name, $callback)
    {
        $this->tasks[$name] = $callback;

        return $this;
    }

    /**
     * @param array $dirs
     * @param string $pattern
     *
     * @return Source
     */
    protected static function src(array $dirs, $pattern = null)
    {
        return new Source($dirs, $pattern);
    }

    /**
     * @param string $path
     *
     * @return PipeInterface
     */
    public static function dest($path)
    {
        return new PipeDest($path);
    }
}
