<?php

use Phulp\Output as out;

// Define the default task
$phulp->task('default', function ($phulp) {
    out::out(out::colorize('Arguments:', 'green'));
    out::out(print_r($phulp->getArguments(), true));

    $phulp->start(['clean', 'iterate_src_folder', 'sync_command', 'async_command']);
    if ($phulp->getArgument('repeat-clean', false)) {
        out::out(out::colorize('Repeating "clean"', 'green'));
        $phulp->start(['clean']);
    }
});

// Define the clean task
$phulp->task('clean', function ($phulp) {
    if (! file_exists('dist')) {
        mkdir('dist');
    }
    $phulp->src(['dist/'])
        ->pipe($phulp->clean());
});

// Define the iterate_src_folder task
$phulp->task('iterate_src_folder', function ($phulp) {
    // Define the source folder
    $phulp->src(['src/'], '/php$/', false)
        ->pipe($phulp->iterate(function ($distFile) {
            out::out(
                out::colorize('Iterated ->', 'green')
                . ' ' . out::colorize(
                    $distFile->getFullPath() . DIRECTORY_SEPARATOR . $distFile->getName(),
                    'blue'
                )
            );
        }))
        ->pipe($phulp->dest('dist/'));
});

// Define the sync_command task
$phulp->task('sync_command', function ($phulp) {
    $command = $phulp->exec(
        'sleep 1 && echo $MSG',
        [
            'env' => [
                'MSG' => 'Sync-command'
            ],
            'cwd' => '/tmp',
            'sync' => true, // defines sync,
            'quiet' => true,
            'onStdOut' => function ($line) { out::out($line); },
            'onStdErr' => function ($line) { },
            'onFinish' => function ($exitCode, $stdOut, $stdErr) { },
        ]
    );

    $exitCode = $command->getExitCode();
    $stdout = $command->getStdout();
    $stderr = $command->getStderr();

    out::out('done');
});

// Define the async_command task
$phulp->task('async_command', function ($phulp) {
    $command = $phulp->exec(
        'sleep 1 && echo $MSG',
        [
            'env' => [
                'MSG' => 'Async-command'
            ],
            'cwd' => '/tmp',
            'sync' => false, // defines async,
            'quiet' => false,
            'onStdOut' => function ($line) { },
            'onStdErr' => function ($line) { },
            'onFinish' => function ($exitCode, $stdOut, $stdErr) { },
        ]
    );

    out::out('done');
});

// Define the watch task
$phulp->task('watch', function ($phulp) {
    // Phulp will watch 'src' folder
    $phulp->watch(
        $phulp->src(['src/'], '/php$/', false),
        function ($phulp, $distFile) {
            out::out(
                out::colorize('File Changed ->', 'green')
                . ' ' . out::colorize(
                    $distFile->getFullPath() . DIRECTORY_SEPARATOR . $distFile->getName(),
                    'blue'
                )
            );
            $phulp->start(['default']);
        }
    );
});
