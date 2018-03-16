<?php

use Phulp\Output as out;

// Define the default task
$phulp->task('default', function ($phulp) {
    $phulp->start(['exec_command']);
});

// Define the exec_command task
$phulp->task('exec_command', function ($phulp) {
    $return = $phulp->exec([
        'command' => 'ls -lh',
        'cwd' => '/tmp'
    ]);

    if ($return['exit_code'] == 0) {
        out::out(out::colorize('Command Output: ' . $return['output'], 'green'));
    } else {
        out::out(out::colorize('Command Output: ' . $return['output'], 'red'));
    }
});