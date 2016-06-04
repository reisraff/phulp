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
        $distFiles = $src->getDistFiles();
        array_walk($distFiles, $this->callback);
    }
}
