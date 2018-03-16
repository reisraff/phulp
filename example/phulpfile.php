<?php

// Define the default task
$phulp->task('default', function ($phulp) {
    $phulp->start(['clean', 'iterate_src_folder', 'sync_command', 'async_command']);
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
            \Phulp\Output::out(
                \Phulp\Output::colorize('Iterated ->', 'green')
                . ' ' . \Phulp\Output::colorize(
                    $distFile->getFullPath() . DIRECTORY_SEPARATOR . $distFile->getName(),
                    'blue'
                )
            );
        }))
        ->pipe($phulp->dest('dist/'));
});

// Define the sync_command task
$phulp->task('sync_command', function ($phulp) {
    $return = $phulp->exec(
        [
            'command' => 'echo $MSG',
            'env' => [
                'MSG' => 'Sync-command'
            ],
            'cwd' => '/tmp'
        ]
    );

    // $return['exit_code']
    // $return['output']
});

// Define the async_command task
$phulp->task('async_command', function ($phulp) {
    $phulp->exec(
        [
            'command' => 'echo $MSG',
            'env' => [
                'MSG' => 'Async-command'
            ],
            'cwd' => '/tmp'
        ],
        true, // defines async
        function ($exitCode, $output) {
            // do something
        }
    );
});

// Define the watch task
$phulp->task('watch', function ($phulp) {
    // Phulp will watch 'src' folder
    $phulp->watch(
        $phulp->src(['src/'], '/php$/', false),
        function ($phulp, $distFile) {
            \Phulp\Output::out(
                \Phulp\Output::colorize('File Changed ->', 'green')
                . ' ' . \Phulp\Output::colorize(
                    $distFile->getFullPath() . DIRECTORY_SEPARATOR . $distFile->getName(),
                    'blue'
                )
            );
            $phulp->start(['default']);
        }
    );
});
