<?php

// Define the default task
$phulp->task('default', function ($phulp) {
    $phulp->exec(
        [
            'command' => 'echo $MSG',
            'env' => [
                'MSG' => 'Assync-command'
            ],
            'cwd' => '/tmp'
        ],
        true,
        function ($exitCode, $output) {
            // do something
        }
    );

    $phulp->start(['clean']);

    // Define the source folder
    $phulp->src(['src/'], '/php$/', false)
        ->pipe($phulp->iterate(function ($distFile) {
            \Phulp\Output::out(\Phulp\Output::colorize($distFile->getName(), 'blue'));
        }))
        ;//->pipe($phulp->dest('dist/'));

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

// Define the clean task
$phulp->task('clean', function ($phulp) {
    // $phulp->src(['dist/'])
    //     ->pipe($phulp->clean());
});

// Define the watch task
$phulp->task('watch', function ($phulp) {
    // Phulp will watch 'src' folder
    $phulp->watch(
        $phulp->src(['src/'], '/php$/', false),
        function ($phulp) {
            $phulp->start(['default']);
        }
    );
});