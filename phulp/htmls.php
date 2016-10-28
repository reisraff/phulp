<?php

$phulp->task('htmls', function ($phulp) use ($config) {
    $phulp->src([$config['src'] . '/app'], '/\.html$/')
        ->pipe($phulp->dest($config['tmp'] . '/app'));
});
