# Documentation

## Create your Phulpfile

You have to create a file called `Phulpfile` in your project root. Alternative names for your `Phulpfile` are
the ones matching the following pattern `[P,p]hulp[Ff]il{e,e.php}` nevertheless the default name __should__ be preferred.

```php
<?php
​
// Define the default task
$phulp->task('default', function ($phulp) {
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

    $phulp->exec(
        [
            'command' => 'echo $MSG',
            'env' => [
                'MSG' => 'Assync-command'
            ],
            'cwd' => '/tmp'
        ],
        true, // defines async
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
        ->pipe($phulp->dest('dist/'));
});

// Define the clean task
$phulp->task('clean', function ($phulp) {
    $phulp->src(['dist/'])
        ->pipe($phulp->clean());
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
```

## Run Phulp:

_If you have not configured the bin-dir:_

```bash
$ vendor/bin/phulp --help
$ vendor/bin/phulp # Will run the `default` task
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
    // here your code
});
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
    function ($phulp) {
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
 * 1st param required: array
 * 2nd param not-required default false: boolean for async
 * 3th param not-required default null: Callback that is called when async command ends
 */

$return = $phulp->exec(
    [
        // the command required
        'command' => 'echo $MSG',

        // the env vars not-required
        'env' => [
            'MSG' => 'Sync-command'
        ],

        // the cwd not-required
        'cwd' => '/tmp'
    ]
);

// $return['exit_code']
// $return['output']

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
```
