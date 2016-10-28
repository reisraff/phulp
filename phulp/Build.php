<?php

use Phulp\Collection;
use Phulp\DistFile;
use Phulp\Source;
use Phulp\Minifier\JsMinifier;
use Phulp\Minifier\CssMinifier;
use Phulp\Filter\Filter;
use Phulp\Dest\Dest;

class Build implements \Phulp\PipeInterface
{
    private $options = [
        'dist_path' => './',
    ];

    public function __construct(array $options = [])
    {
        $this->options = array_merge($this->options, $options);
    }

    public function execute(Source $src)
    {
        foreach ($src->getDistFiles() as $distFile) {
            $this->process($distFile);
        }
    }

    private function process(DistFile $distFile)
    {
        $root = $distFile->getBasepath();

        $matches = [];

        preg_match_all(
            '/<!--\s*build:[a-zA-Z0-9]+\s*[a-zA-Z0-9\/-_\.]+\s-->(.*?)<!--\s*endbuild\s*-->/s',
            $distFile->getContent(),
            $matches
        );

        foreach ($matches[0] as $match) {
            $ext = preg_replace('/.*build:([a-zA-Z0-9]+).*/s', '$1', $match);
            $dest = preg_replace('/.*build:[a-zA-Z0-9]+\s*([a-zA-Z0-9\/-_\.]+).*/s', '$1', $match);

            if ($ext == 'js') {
                $scripts = [];

                preg_match_all('/src=[\"\'](.*?)[\"\']/s', $match, $scripts);
                $scripts = $scripts[1];

                $jsDistFiles = new Collection([], DistFile::class);

                foreach ($scripts as $script) {
                    $jsDistFiles->add(
                        new DistFile(
                            file_get_contents($root . '/' . $script),
                            'dummy.js'
                        )
                    );
                }

                $src = new \Phulp\Source([__DIR__]);
                $src->pipe(new Filter(function () {
                    return true;
                }));
                $src->setDistFiles($jsDistFiles);
                $src->pipe(new JsMinifier(['join' => true]));
                $jsDistFile = $src->getDistFiles()->first();
                $jsDistFile->setDistpathname($dest);
                $src->pipe(new Dest($this->options['dist_path']));

                $distFile->setContent(
                    str_replace(
                        $match,
                        '<script src="'
                        . $this->options['dist_path']
                        . DIRECTORY_SEPARATOR
                        . $jsDistFile->getDistpathname()
                        . '"></script>',
                        $distFile->getContent()
                    )
                );
            }

            if ($ext == 'css') {
                $scripts = [];

                preg_match_all('/href=[\"\'](.*?)[\"\']/s', $match, $scripts);
                $scripts = $scripts[1];

                $cssDistFiles = new Collection([], DistFile::class);

                foreach ($scripts as $script) {
                    $cssDistFiles->add(
                        new DistFile(
                            file_get_contents($root . '/' . $script),
                            'dummy.css'
                        )
                    );
                }

                $src = new \Phulp\Source([__DIR__]);
                $src->pipe(new Filter(function () {
                    return true;
                }));
                $src->setDistFiles($cssDistFiles);
                $src->pipe(new CssMinifier(['join' => true]));
                $cssDistFile = $src->getDistFiles()->first();
                $cssDistFile->setDistpathname($dest);
                $src->pipe(new Dest($this->options['dist_path']));

                $distFile->setContent(
                    str_replace(
                        $match,
                        '<link rel="stylesheet" href="'
                        . $this->options['dist_path']
                        . DIRECTORY_SEPARATOR
                        . $cssDistFile->getDistpathname()
                        . '">',
                        $distFile->getContent()
                    )
                );
            }
        }
    }
}
