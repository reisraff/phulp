<?php

namespace Phulp;

interface PipeInterface
{
    /**
     * @param Source $source
     */
    public function execute(Source $source);
}
