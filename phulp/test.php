<?php

use Phulp\Phulp;

$phulp->task('test', function ($phulp) {
    $phulp->start(['lint:test', 'phpcs:test', 'unit:test']);
});

$phulp->task('lint:test', function ($phulp) {
    $error = false;
    $phulp->src(sprintf('%s/../src/**/*php', __DIR__))
        ->pipe($phulp->iterate(function ($file) use ($error, $phulp) {
            $file = $file->getFullPath() . DIRECTORY_SEPARATOR . $file->getName();
            $cmd = $phulp->exec(sprintf('php -l %s', $file));
            if ($cmd->getExitCode()) {
                $error = true;
            }
        }));

    if ($error) {
        throw new \Exception('lint:test failed');
    }
});

$phulp->task('phpcs:test', function ($phulp) {
    $command = $phulp->exec(
        __DIR__ . '/../bin/phpcs --standard=PSR2 --extensions=php ' . __DIR__ . '/../src'
    );

    if ($command->getExitCode()) {
        throw new \Exception('phpcs:test failed');
    }
});

$phulp->task('unit:test', function ($phulp) {
    $command = $phulp->exec(
        __DIR__ . '/../bin/phpunit --verbose --debug',
        [
            'cwd' => __DIR__ . '/../',
        ]
    );

    if ($command->getExitCode()) {
        throw new \Exception('unit:test failed');
    }
});
