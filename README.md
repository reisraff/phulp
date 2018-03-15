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

// filepath: /path/for/your/phulpfile.php

// Define the default task
$phulp->task('default', function ($phulp) {
    $phulp->start(['clean', 'iterate_src_folder', 'sync_command', 'assync_command']);
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

// Define the assync_command task
$phulp->task('assync_command', function ($phulp) {
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
```

##### Run:

Run the phulp over the `Phulpfile` directory

_If you have not configured the bin-dir:_

```bash
$ vendor/bin/phulp --help
$ vendor/bin/phulp # Will run the `default` task
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
$ bin/phing
```

### TODO

The "Issues" page from this repository is being used for TO-DO management.

## Credits

[@reisraff](http://www.twitter.com/reisraff)
