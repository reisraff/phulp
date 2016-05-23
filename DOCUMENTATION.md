# Documentation

## Create your PhulpFile.php

You must implements the \Phulp\Phulp and your class must be named as PhulpFile.

```php
<?php
​
use Phulp\Phulp;
​
class PhulpFile extends Phulp
{
    public function define()
    {
        // Define the default task
        Phulp::task('default', function () {
            Phulp::start(['clean']);
​
            // Define the source folder
            Phulp::src(['src/'], '/php$/', false)
                ->pipe(Phulp::iterate(function ($distFile) {
                    \Phulp\Output::out($distFile->getName(), 'blue');
                }))
                ->pipe(Phulp::dest('dist/'));
        });
​
        // Define the clean task
        Phulp::task('clean', function () {
            Phulp::src(['dist/'])
                ->pipe(Phulp::clean());
        });
​
        // Define the watch task
        Phulp::task('watch', function () {
            // Phulp will watch 'src' folder
            Phulp::watch(
                Phulp::src(['src/'], '/php$/', false),
                ['default']
            );
        });
    }
}

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

### Phulp::task()

Instantiate yours tasks.

```php
<?php

Phulp::task('name', function () {
    // here your code
});

```

### Phulp::clean()

Return for you an instance of \Phulp\PipeIterate that will iterate all src files and delete your parent directory.

```php
<?php

Phulp::src(['dist/'])
    ->pipe(Phulp::clean());

```

### Phulp::src()

Find files for manage them, and you can pipe them also.

```php
<?php

/**
 * 1st param required: array of directories
 * 2nd param not-required defualt null: pattern
 * 3th param not-required default true: boolean for recursion
 */
Phulp::src(['src/'], '/pattern/', false);

```

Piping:

```php
<?php

Phulp::src(['src/'], '/pattern/', false)
    // ->pipe(\Phulp\PipeInterface)

```

### Phulp::iterate()

Provide iteration with src files using clousure:

```php
<?php

Phulp::src(['src/'], '/pattern/', false)
    ->pipe(Phulp::iterate(function ($distFile) {
        /**
         * @var \Phulp\DistFile $distFile
         */
    }));

```

### Phulp::dest()

Used to pipe src files and the src files will be placed for the directory passed as parameter in dest():

```php
<?php

Phulp::src(['src/'], '/pattern/', false)
    ->pipe(Phulp::dest('dist/'))

```

### Phulp::watch()

Watch files and do something when a file changes.

```php
<?php

Phulp::watch(
    Phulp::src(['src/'], '/php$/', false),
    ['default'] // The "default" task will be emited when the src was changed
);

```

### Phulp::start()

Starts synchronously the tasks passed by parameter:

```php
<?php

Phulp::start(['default', 'watch']);

```
