<?php

$phulp->task('others', function ($phulp) use ($config) {
    $phulp->src(
        [$config['src']],
        '/.+(?<!html|css|js|scss)$/'
    )
        ->pipe($phulp->dest($config['tmp']));
});
