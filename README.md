<p align="center"><img src="https://raw.githubusercontent.com/reisraff/phulp/master/phulp.png" alt="phulp" /></p>

<p align="center">The task manager for php</p>

[![Latest Stable Version](https://poser.pugx.org/reisraff/phulp/v/stable)](https://packagist.org/packages/reisraff/phulp)
[![Total Downloads](https://poser.pugx.org/reisraff/phulp/downloads)](https://packagist.org/packages/reisraff/phulp)
[![Latest Unstable Version](https://poser.pugx.org/reisraff/phulp/v/unstable)](https://packagist.org/packages/reisraff/phulp)
[![License](https://poser.pugx.org/reisraff/phulp/license)](https://packagist.org/packages/reisraff/phulp)
[![Build Status](https://api.travis-ci.org/reisraff/phulp.svg?branch=master)](https://travis-ci.org/reisraff/phulp)

### Why

Sometimes I need a tool like Gulp for my PHP projects, but I don't want to install `npm` only to install Gulp. I thought "I need something like Gulp, but in PHP". After a little research I found Phing, but it's not focused in minification and management for CSS/JS and related frontend stuff.

Well, I decided to write Phulp, the PHP port of Gulp! And a little curiosity: it's faster than Gulp.

**PS: I made benchs using PHP 7**

### Documentation

#### Plugins

Like Gulp we also have plugins, and you also can create your own.

Available plugins you can find in the plugin section over the [Phulp Page](https://reisraff.github.io/phulp).

To make your plugin available in the Phulp plugin page, add the keyword "phulpplugin" in your composer.json file of your project, and don't forget to let a cool composer.json description.

And tag your github project with the tags ["phulpplugin"](https://github.com/topics/phulpplugin), and ["phulp"](https://github.com/topics/phulp), to be searchable on github.

#### Usage

##### Install:

```bash
$ composer require --dev reisraff/phulp
```

##### Create your `Phulpfile` (the configuration file, that describes all your tasks):

```php
<?php

use Phulp\Output as out;

// Define the default task
$phulp->task('default', function ($phulp) {
    out::outln(out::colorize('Arguments:', 'green'));
    out::outln(print_r($phulp->getArguments(), true));

    $phulp->start(['clean', 'iterate_src_folder', 'sync_command', 'async_command']);
    if ($phulp->getArgument('repeat-clean', false)) {
        out::outln(out::colorize('Repeating "clean"', 'green'));
        $phulp->start(['clean']);
    }
});

// Define the clean task
$phulp->task('clean', function ($phulp) {
    if (! file_exists('dist')) {
        mkdir('dist');
    }
    $phulp->src(['dist/*'])
        ->pipe($phulp->clean());
});

// Define the iterate_src_folder task
$phulp->task('iterate_src_folder', function ($phulp) {
    // Define the source folder
    $phulp->src(['src/*php'])
        ->pipe($phulp->iterate(function ($file) {
            out::outln(sprintf(
                '%s %s',
                out::colorize('Iterated ->', 'green'),
                out::colorize($file->getFullPath() . DIRECTORY_SEPARATOR . $file->getName(), 'blue')
            ));
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
            'onStdOut' => function ($line) { out::outln($line); },
            'onStdErr' => function ($line) { },
            'onFinish' => function ($exitCode, $stdOut, $stdErr) { },
        ]
    );

    $exitCode = $command->getExitCode();
    $stdout = $command->getStdout();
    $stderr = $command->getStderr();

    out::outln('done');
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

    out::outln('done');
});

// Define the watch task
$phulp->task('watch', function ($phulp) {
    // Phulp will watch 'src' folder
    $phulp->watch(
        $phulp->src(['src/*php']),
        function ($phulp, $distFile) {
            out::outln(sprintf(
                '%s %s',
                out::colorize('File Changed ->', 'green'),
                out::colorize($distFile->getFullPath() . DIRECTORY_SEPARATOR . $distFile->getName(), 'blue')
            ));
            $phulp->start(['default']);
        }
    );
});
```

##### Run:

Run the phulp over the `Phulpfile` directory

_If you have not configured the bin-dir:_

```bash
$ vendor/bin/phulp --help
$ vendor/bin/phulp # Will run the `default` task
$ vendor/bin/phulp --arg=repeat-clean:true # Will run the `default` task with the argument repeat-clean with value `true`
$ vendor/bin/phulp --autoload=/my/autoload/path/autoload.php # Will run the `default` task adding a alternative autoload php file
$ vendor/bin/phulp watch # Will run the `watch` task
```

##### The full documentation:

[Docs](https://github.com/reisraff/phulp/blob/master/DOCUMENTATION.md)

##### Example:

[https://github.com/reisraff/phulp/blob/master/example/phulpfile.php](https://github.com/reisraff/phulp/blob/master/example/phulpfile.php)

Run the example file:

```bash
$ composer install
$ cd example
$ ../bin/phulp
$ ../bin/phulp watch
```

### Contributors Guide

#### Clone

```bash
$ git clone git@github.com:reisraff/phulp.git
$ cd phulp
$ composer install
```

#### Tests

_First install the dependencies, and after you can run:_

```bash
$ bin/phulp test
```

### TODO

The "Issues" page from this repository is being used for TO-DO management.

## Credits

[@reisraff](http://www.twitter.com/reisraff)
