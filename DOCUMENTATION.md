# Documentation

## Create your Phulpfile

You have to create a file called `Phulpfile` in your project root. Alternative names for your `Phulpfile` are
the ones matching the following pattern `[P,p]hulp[Ff]il{e,e.php}` nevertheless the default name __should__ be preferred.

```php
<?php

// filepath: /path/for/your/phulpfile.php

use Phulp\Output as out;

// Define the default task
$phulp->task('default', function ($phulp) {
    out::out(out::colorize('Arguments:', 'green'));
    out::out(print_r($phulp->getArguments(), true));

    $phulp->start(['clean', 'iterate_src_folder', 'sync_command', 'async_command']);
    if ($phulp->getArgument('repeat-clean', false)) {
        out::out(out::colorize('Repeating "clean":', 'green'));
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
```

## Run Phulp:

_If you have not configured the bin-dir:_

```bash
$ vendor/bin/phulp --help
$ vendor/bin/phulp # Will run the `default` task
$ vendor/bin/phulp --arg=repeat-clean:true # Will run the `default` task with the argument repeat-clean with value `true`
$ vendor/bin/phulp --autoload=/my/autoload/path/autoload.php # Will run the `default` task adding a alternative autoload php file
$ vendor/bin/phulp watch # Will run the `watch` task
```

## Find for what plugin you really need:

[Phulp - Plugin Page](https://reisraff.github.io/phulp/#!/plugins)

## Methods

### $phulp->task()

Instantiate yours tasks.

```php
<?php

$phulp->task('name', function ($phulp) {
    /** @var \Phulp\Phulp $phulp */
    // here your code
});
```

### $phulp->getArguments()

Get all arguments.

```php
<?php

$arguments = $phulp->getArguments();
```

### $phulp->getArgument()

Get some argument value.

```php
<?php

$argument = $phulp->getArgument('argument-name', 'default-value-if-argument-does-not-exists');
```

### $phulp->clean()

Return for you an instance of `\Phulp\PipeIterate` that will iterate all src files and delete your parent directory.

```php
<?php

$phulp->src(['dist/'])
    ->pipe($phulp->clean());
```

### $phulp->src()

Find files for manage them, and you can pipe them also.

```php
<?php

/**
 * 1st param required: array of directories
 * 2nd param not-required default null: pattern
 * 3th param not-required default true: boolean for recursion
 */
$phulp->src(['src/'], '/pattern/', false);
```

Piping:

```php
<?php

$phulp->src(['src/'], '/pattern/', false)
    // ->pipe(\Phulp\PipeInterface)
```

### $phulp->iterate()

Provide iteration with src files using clousure:

```php
<?php

$phulp->src(['src/'], '/pattern/', false)
    ->pipe($phulp->iterate(function ($distFile) {
        /** @var \Phulp\DistFile $distFile */
    }));
```

### $phulp->dest()

Used to pipe src files and the src files will be placed for the directory passed as parameter in dest():

```php
<?php

$phulp->src(['src/'], '/pattern/', false)
    ->pipe($phulp->dest('dist/'))
```

### $phulp->watch()

Watch files and do something when a file changes.

```php
<?php

$phulp->watch(
    $phulp->src(['src/'], '/php$/', false),
    function ($phulp, $distFile) {
        /** @var \Phulp\Phulp $phulp */
        /** @var \Phulp\DistFile $distFile */
        // here your code
    }
);

```

### $phulp->start()

Starts synchronously the tasks passed by parameter:

```php
<?php

$phulp->start(['default', 'watch']);
```

### $phulp->exec()

Execute an external command:

```php
<?php

/**
 * 1st param required string
 * 2nd param not-required array
 */

$command = $phulp->exec(
    // the command required
    'echo $MSG',
    [
        'cwd' => getcwd(), // <= default
        'env' => [], // <= default ['HOME' => '/home/my-home']
        'quiet' => false, // <= default
        'sync' => true, // <= default
        'onStdOut' => null, // <= default function ($line) {}
        'onStdErr' => null, // <= default function ($line) {}
        'onFinish' => null, // <= default function ($exitCode, $stdOut, $stdErr) {}
    ]
);

$command->write('write to stdin');

$exitCode = $command->getExitCode();
$stdout = $command->getStdout();
$stderr = $command->getStderr();

```
