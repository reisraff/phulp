<?php

$config = require 'config.php';

require 'InjectBowerVendor.php';
require 'Build.php';

use Phulp\ScssCompiler\ScssCompiler;
use Phulp\Inject\Inject;
use Phulp\AngularTemplateCache\AngularTemplateCache;
use Phulp\AngularFileSort\AngularFileSort;
use Phulp\Output as out;
use Phulp\Server\Server;

$phulp->task('inject', function ($phulp) use ($config) {
    $phulp->start(['others', 'fonts', 'scripts', 'styles', 'htmls']);

    $injectStyles = $phulp->src([$config['tmp'] . '/app'], '/.css$/');

    $injectScripts = $phulp->src([$config['src'] . '/app'], '/.+(?<!spec|mock)\.js$/')
        ->pipe(new AngularFileSort)
        ->pipe($phulp->dest($config['tmp'] . '/app'));

    $filterFilename = function ($filename) {
        return 'app/' . ltrim($filename, '/');
    };

    $phulp = $phulp->src([$config['src']], '/html$/', false)
        ->pipe(new Inject($injectStyles->getDistFiles(), ['filter_filename' => $filterFilename]))
        ->pipe(new Inject($injectScripts->getDistFiles(), ['filter_filename' => $filterFilename]))
        ->pipe(new InjectBowerVendor([
            'bowerPath' => $config['bower_components'],
            'distVendorPath' => $config['tmp'] . '/vendor/',
            'injectOptions' => [
                'filter_filename' => function ($filename) {
                    return 'vendor/' . $filename;
                },
                'tagname' => 'bower'
            ]
        ]))
        ->pipe($phulp->dest($config['tmp'] . '/'));
});

$phulp->task('partials', function ($phulp) use ($config) {
    $phulp->src(
        [
            $config['src'] . '/app',
        ],
        '/\.html$/'
    )
        ->pipe($phulp->iterate(function ($distFile) {
            $distFile->setContent(
                preg_replace(
                    ['/\>[^\S ]+/s', '/[^\S ]+\</s', '/(\s)+/s'],
                    ['>', '<', '\\1'],
                    $distFile->getContent()
                )
            );
        }))
        ->pipe(new AngularTemplateCache(
            'templateCacheHtml.js',
            [
                'module' => 'app',
                'root' => 'app'
            ]
        ))
        ->pipe($phulp->dest($config['tmp']));
});

$phulp->task('dist', function ($phulp) use ($config) {
    $phulp->start(['inject', 'partials']);

    $phulp->src([$config['tmp'] . '/fonts'])
        ->pipe($phulp->dest($config['dist'] . '/fonts'));

    $phulp->src([$config['src']], '/favicon.png/')
        ->pipe($phulp->dest('./'));

    $partialsInjectFile = $phulp->src(
        [$config['tmp']],
        '/templateCacheHtml\.js$/',
        false
    );

    $phulp->src(
        [$config['tmp']],
        '/html$/',
        false
    )
        ->pipe(
            new Inject(
                $partialsInjectFile->getDistFiles(),
                [
                    'starttag' => '<!-- inject:partials -->',
                ]
            )
        )
        ->pipe(new Build(['dist_path' => $config['dist']]))
        ->pipe($phulp->iterate(function ($distFile) {
            $distFile->setContent(
                preg_replace(
                    '/\<\!\-\-(.+?)\-\-\>/',
                    null,
                    $distFile->getContent()
                )
            );
            $distFile->setContent(
                preg_replace(
                    ['/\>[^\S ]+/s', '/[^\S ]+\</s', '/(\s)+/s'],
                    ['>', '<', '\\1'],
                    $distFile->getContent()
                )
            );
        }))
        ->pipe($phulp->dest('./'));

    $phulp->src(
        [$config['dist'] . '/styles'],
        '/vendor\.css$/',
        false
    )
        ->pipe($phulp->iterate(function ($distFile) {
            $distFile->setContent(
                str_replace('../fonts/bootstrap/', '../fonts/', $distFile->getContent())
            );
        }))
        ->pipe($phulp->dest($config['dist'] . '/styles'));

    $phulp->src(
        [$config['tmp']],
        '/ico$/',
        false
    )
        ->pipe($phulp->dest($config['dist']));
});

$phulp->task('serve:dist', function ($phulp) use ($config) {
    $phulp->start(['build']);

    $server = new \Phulp\Server\Server(
        [
            'path' => realpath($config['dist'] . '/..'),
            'port' => '8000'
        ],
        $phulp->getLoop()
    );
});

$phulp->task('clean-dist', function ($phulp) use ($config) {
    if (!file_exists($config['dist'])) {
        mkdir($config['dist']);
    }

    $phulp->src([$config['dist']])
        ->pipe($phulp->clean());
});

$phulp->task('clean-tmp', function ($phulp) use ($config) {
    if (!file_exists($config['tmp'])) {
        mkdir($config['tmp']);
    }

    $phulp->src([$config['tmp']])
        ->pipe($phulp->clean());
});

$phulp->task('build', function ($phulp) {
    $phulp->start(['clean-tmp', 'clean-dist', 'dist']);
});

$phulp->task('default', function ($phulp) {
    $phulp->start(['build']);
});

$phulp->task('styles', function ($phulp) use ($config) {
    $injectFiles = $phulp->src([$config['src'] . '/app'], '/.+(?<!app)\.scss$/');

    $phulp->src([$config['src'] . '/app'], '/app\.scss/', false)
        ->pipe(new Inject($injectFiles->getDistFiles()))
        ->pipe(new ScssCompiler(['import_paths' => ['src/src/app/']]))
        ->pipe($phulp->dest($config['tmp'] . '/app/'));
});

$phulp->task('scripts', function ($phulp) use ($config) {
    $phulp->src(
        [$config['src'] . '/app'],
        '/.+(?<!spec|mock)\.js$/'
    )
        ->pipe($phulp->dest($config['tmp'] . '/app'));
});

$phulp->task('htmls', function ($phulp) use ($config) {
    $phulp->src([$config['src'] . '/app'], '/\.html$/')
        ->pipe($phulp->dest($config['tmp'] . '/app'));
});

$phulp->task('fonts', function ($phulp) use ($config) {
  return $phulp->src(
        [$config['bower_components']],
        '/\.(eot|svg|ttf|woff|woff2)$/'
    )
        ->pipe($phulp->iterate(function ($distFile) {
            $distFile->setDistpathname($distFile->getName());
        }))
        ->pipe($phulp->dest($config['tmp'] . '/fonts'));
});

$phulp->task('others', function ($phulp) use ($config) {
    $phulp->src(
        [$config['src']],
        '/.+(?<!html|css|js|scss)$/'
    )
        ->pipe($phulp->dest($config['tmp']));
});

$phulp->task('serve', function ($phulp) use ($config) {
    $phulp->start(['clean-tmp', 'inject']);

    if (! $path = realpath($config['tmp'])) {
        out::err(out::colorize('The build wasn\'t sucessfully', 'red'));
        exit(1);
    }

    $server = new Server(
        [
            'path' => $path,
            'port' => '8000'
        ],
        $phulp->getLoop()
    );

    $phulp->watch(
        $phulp->src([$config['src']], '/(css|scss)$/'),
        function ($phulp) {
            $phulp->start(['styles']);
        }
    );

    $phulp->watch(
        $phulp->src([$config['src']], '/(js)$/'),
        function ($phulp) {
            $phulp->start(['scripts']);
        }
    );

    $phulp->watch(
        $phulp->src([$config['src'] . '/app'], '/\.html$/'),
        function ($phulp) {
            $phulp->start(['htmls']);
        }
    );

    $phulp->watch(
        $phulp->src(
            [$config['src']],
            '/.+(?<!html|css|js|scss)$/'
        ),
        function ($phulp) {
            $phulp->start(['others']);
        }
    );
});
