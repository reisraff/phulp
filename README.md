# phulp [_BETA_]

[![Latest Stable Version](https://poser.pugx.org/reisraff/phulp/v/stable)](https://packagist.org/packages/reisraff/phulp)
[![Total Downloads](https://poser.pugx.org/reisraff/phulp/downloads)](https://packagist.org/packages/reisraff/phulp)
[![Latest Unstable Version](https://poser.pugx.org/reisraff/phulp/v/unstable)](https://packagist.org/packages/reisraff/phulp)
[![License](https://poser.pugx.org/reisraff/phulp/license)](https://packagist.org/packages/reisraff/phulp)
[![Build Status](https://travis-ci.org/reisraff/phulp.svg?branch=master)](https://travis-ci.org/reisraff/phulp)

The task manager for php

### Documentation

#### Usage

##### Install:

```bash
$ composer require reisraff/phulp:dev-master
```

##### Create your `PhulpFile.php`:

```php
<?php

use Phulp\Phulp;

class PhulpFile extends Phulp
{
    public function define()
    {
        Phulp::task('default', function () {
            Phulp::start(['clean']);

            Phulp::src(['src/'], '/php$/', false)
                // ->pipe(\Phulp\PipeInterface)
                ->pipe(Phulp::iterate(function ($distFile) {
                    \Phulp\Output::out($distFile->getName(), 'blue');
                }))
                ->pipe(Phulp::dest('dist/'));
        });

        Phulp::task('clean', function () {
            Phulp::src(['dist/'])
                ->pipe(Phulp::clean());
        });

        Phulp::task('watch', function () {
            Phulp::watch(
                Phulp::src(['src/'], '/php$/', false),
                ['default']
            );
        });
    }
}

```

##### Run:

_If you have not configured the bin-dir:_

```bash
$ vendor/bin/phulp # Will run the `default` task
$ vendor/bin/phulp watch # Will run the `watch` task
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

The "Issues" page from this repository is being used for TO-DO management, just search for the "to-do" tag.

## Credits

[@reisraff](http://www.twitter.com/reisraff)
