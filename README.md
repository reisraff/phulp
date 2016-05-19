# phulp [_BETA_]

[![Latest Stable Version](https://poser.pugx.org/reisraff/phulp/v/stable)](https://packagist.org/packages/reisraff/phulp)
[![Total Downloads](https://poser.pugx.org/reisraff/phulp/downloads)](https://packagist.org/packages/reisraff/phulp)
[![Latest Unstable Version](https://poser.pugx.org/reisraff/phulp/v/unstable)](https://packagist.org/packages/reisraff/phulp)
[![License](https://poser.pugx.org/reisraff/phulp/license)](https://packagist.org/packages/reisraff/phulp)
[![Build Status](https://travis-ci.org/reisraff/phulp.svg?branch=master)](https://travis-ci.org/reisraff/phulp)

The task manager for php

### Documentation

#### Usage

Install:

```bash
$ composer require reisraff/phulp:dev-master
```

Create your `PhulpFile.php`:

```php
<?php

use Phulp\Phulp;

class PhulpFile extends Phulp
{
    public function define()
    {
        Phulp::task('default', function () {
            Phulp::src(['src/'], '/php$/', false)
                // ->pipe(\Phulp\PipeInterface)
                ->pipe(Phulp::iterate(function ($distFile) {
                    echo $distFile->getName() . PHP_EOL;
                }))
                ->pipe(Phulp::dest('dist'));
        });

        Phulp::task('myTask', function () {
            Phulp::src(['src/'], '/php$/', false)
                // ->pipe(\Phulp\PipeInterface)
                ->pipe(Phulp::iterate(function ($distFile) {
                    echo $distFile->getName() . PHP_EOL;
                }))
                ->pipe(Phulp::dest('dist'));
        });
    }
}

```

Run:

If you have not configured the bin-dir:

```bash
$ vendor/bin/phulp # Will run the `default` task
$ vendor/bin/phulp myTask # Will run the `myTask` task
```

### Contributors Guide

#### Clone

```bash
$ git clone git@github.com:reisraff/phulp.git
$ cd phulp
$ composer install
```

#### Tests

First install the dependencies, and after you can run:

```bash
$ bin/phing
```

### TODO

The "Issues" page from this repository is being used for TO-DO management, just search for the "to-do" tag.
