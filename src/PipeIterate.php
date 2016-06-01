<?php

namespace Phulp;

class PipeIterate implements PipeInterface
{
    /**
     * @var callable $callback
     */
    private $callback;

    /**
     * @param callable $callback
     */
    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    /**
     * @inheritdoc
     */
    public function execute(Source $src)
    {
        array_walk($src->getDistFiles(), $this->callback);
    }
}
