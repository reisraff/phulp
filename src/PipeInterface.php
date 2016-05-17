<?php

namespace Phulp;

interface PipeInterface
{
    /**
     * @param Source $source
     */
    public function do(Source $source);
}
