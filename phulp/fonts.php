<?php

$phulp->task('fonts', function ($phulp) use ($config) {
  return $phulp->src(
        [$config['bower_components']],
        '/\.(eot|svg|ttf|woff|woff2)$/'
    )
        ->pipe($phulp->iterate(function ($distFile) {
            $distFile->setDistpathname($distFile->getName());
        }))
        ->pipe($phulp->dest($config['tmp'] . '/fonts'));
});
