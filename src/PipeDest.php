<?php

namespace Phulp;

class PipeDest implements PipeInterface
{
    /**
     * @var string $path
     */
    private $path;

    /**
     * @param string $path
     */
    public function __construct($path)
    {
        $this->path = $path;

        self::createDir($path);
    }

    /**
     * @inheritdoc
     */
    public function do(Source $src)
    {
        foreach ($src->getDistFiles() as $file) {
            if (!empty($file->getDir())) {
                self::createDir($this->path . DIRECTORY_SEPARATOR . $file->getDir());
            }

            file_put_contents($this->path . DIRECTORY_SEPARATOR . $file->getName(), $file->getContent());
        }
    }

    /**
     * @param $path
     */
    public static function createDir($path)
    {
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
    }
}
