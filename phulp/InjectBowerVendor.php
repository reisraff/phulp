<?php

use Phulp\ScssCompiler\ScssCompiler;
use Phulp\Inject\Inject;
use Phulp\Filter\Filter;
use Phulp\Dest\Dest;

class InjectBowerVendor implements \Phulp\PipeInterface
{
    private $options = [
        'bowerPath' => 'bower_components/',
        'distVendorPath' => 'dist/',
        'filter' => null,
        'injectOptions' => []
    ];

    public function __construct(array $options = [])
    {
        $this->options = array_merge($this->options, $options);
    }

    public function execute(\Phulp\Source $src)
    {
        if (! file_exists($this->options['bowerPath'])) {
            // error
        }

        $jsStack = [];
        $cssStack = [];
        $cssPaths = [];
        foreach ((new DirectoryIterator($this->options['bowerPath'])) as $item) {
            if ($item->isDir() && ! $item->isDot()) {
                $file = $item->getPathname() . '/bower.json';
                if (! file_exists($file)) {
                    // error
                }

                $bowerJson = json_decode(file_get_contents($file), true);

                if (! isset($bowerJson['main'])) {
                    // error
                }

                $bowerJson['main'] = is_string($bowerJson['main']) ?
                    (array) $bowerJson['main'] :
                    $bowerJson['main'];

                foreach ($bowerJson['main'] as $file) {
                    $filename = $item->getPathname() . '/' . ltrim($file, '/');
                    if (! file_exists($filename)) {
                        // error
                    }

                    $realpath = realpath($filename);
                    $fullpath = substr($realpath, 0, strrpos($realpath, '/'));
                    $relativepath = str_replace(
                        $this->options['bowerPath'],
                        null,
                        $filename
                    );
                    $relativepath = ltrim(substr($relativepath, 0, strrpos($relativepath, '/')), '/');
                    $filename = substr($realpath, strrpos($realpath, '/') + 1);

                    $distFile = new \Phulp\DistFile(
                        file_get_contents($realpath),
                        $filename,
                        $fullpath,
                        $relativepath
                    );

                    if ($filter = $this->options['filter']) {
                        if (! is_callable($filter)) {
                            // error
                        }

                        if (! $filter($getDistpathname)) {
                            continue;
                        }
                    }

                    if (preg_match('/js$/', $distFile->getName())) {
                        $jsStack[] = $distFile;
                    } elseif (preg_match('/(css|scss)$/', $distFile->getName())) {
                        $cssStack[] = $distFile;
                        $cssPaths[] = $item->getPathname() . '/' .
                            substr(
                                $distFile->getRelativePath(),
                                strpos($distFile->getRelativePath(), '/') + 1)
                            . '/';
                    }
                }
            }
        }

        $bowerDistFiles = new \Phulp\Collection($jsStack, \Phulp\DistFile::class);

        $srcBower = new \Phulp\Source([$this->options['bowerPath']]);
        $srcBower->pipe(new Filter(function () {
            return true;
        }));
        $srcBower->setDistFiles($bowerDistFiles);
        $srcBower->pipe(new AngularFileSort);

        foreach ($cssStack as $cssDistFile) {
            $srcBower->addDistFile($cssDistFile);
        }

        $srcBower->pipe(new ScssCompiler(['import_paths' => $cssPaths]))
            ->pipe(new Dest($this->options['distVendorPath']));

        (new Inject($bowerDistFiles, $this->options['injectOptions']))->execute($src);
    }
}
