# Documentation

## Create your PhulpFile.php

You have to create a file called `PhulpFile.php` in your project root.

```php
<?php
​
// Define the default task
$phulp->task('default', function ($phulp) {
    $phulp->start(['clean']);
​
    // Define the source folder
    $phulp->src(['src/'], '/php$/', false)
        ->pipe($phulp->iterate(function ($distFile) {
            \Phulp\Output::out($distFile->getName(), 'blue');
        }))
        ->pipe($phulp->dest('dist/'));
});
​
// Define the clean task
$phulp->task('clean', function ($phulp) {
    $phulp->src(['dist/'])
        ->pipe($phulp->clean());
});
​
// Define the watch task
$phulp->task('watch', function ($phulp) {
    // Phulp will watch 'src' folder
    $phulp->watch(
        $phulp->src(['src/'], '/php$/', false),
        ['default']
    );
});
```

## Run Phulp:

_If you have not configured the bin-dir:_

```bash
$ vendor/bin/phulp # Will run the `default` task
$ vendor/bin/phulp watch # Will run the `watch` task
```

## Find for what plugin you really need:

[Phulp - Plugin Page](https://reisraff.github.io/phulp/dist/#!/plugins)

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
    ['default'] // The "default" task will be emited when the src was changed
);
```

### $phulp->start()

Starts synchronously the tasks passed by parameter:

```php
<?php

$phulp->start(['default', 'watch']);
```
