<?php

$phulp->task('scripts', function ($phulp) use ($config) {
    $phulp->src(
        [$config['src'] . '/app'],
        '/.+(?<!spec|mock)\.js$/'
    )
        ->pipe($phulp->dest($config['tmp'] . '/app'));
});
