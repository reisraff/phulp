<?php

use Phulp\Phulp;

$phulp->task('test', function ($phulp) {
    $phulp->start(['lint:test', 'phpcs:test', 'unit:test']);
});

$phulp->task('lint:test', function ($phulp) {
    $return = $phulp->exec([
        'command' => 'find -L ' . __DIR__ . '/../src -name "*.php" -print0 | xargs -0 -n 1 -P 4 php -l'
    ]);

    if ($return['exit_code']) {
        throw new \Exception('lint:test failed');
    }
});

$phulp->task('phpcs:test', function ($phulp) {
    $return = $phulp->exec([
        'command' => __DIR__ . '/../bin/phpcs --standard=PSR2 --extensions=php ' . __DIR__ . '/../src'
    ]);

    if ($return['exit_code']) {
        throw new \Exception('phpcs:test failed');
    }
});

$phulp->task('unit:test', function ($phulp) {
    $return = $phulp->exec([
        'command' => __DIR__ . '/../bin/phpunit --verbose --debug',
        'cwd' => __DIR__ . '/../',
    ]);

    if ($return['exit_code']) {
        throw new \Exception('unit:test failed');
    }
});
