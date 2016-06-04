<?php

namespace Phulp;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class Source
{
    /**
     * @var DistFile[] $distFiles
     */
    private $distFiles = [];

    /**
     * @var SplFileInfo[] $dirs
     */
    private $dirs = [];

    /**
     * @param array $dirs
     * @param string $pattern
     * @param boolean $recursive
     */
    public function __construct(array $dirs, $pattern = '', $recursive = false)
    {
        $finder = new Finder;
        if (!$recursive) {
            $finder->depth('== 0');
        }
        if ($pattern) {
            $finder->name($pattern);
        }
        $finder->in($dirs);

        /** @var SplFileInfo $file */
        foreach ($finder as $file) {
            if ($file->isDir()) {
                $this->dirs[] = $file;
                continue;
            }

            $realPath = $file->getRealPath();
            $dsPos = strrpos($realPath, DIRECTORY_SEPARATOR);
            $this->distFiles[] = new DistFile(
                file_get_contents($realPath),
                substr($realPath, $dsPos + 1),
                substr($realPath, 0, $dsPos),
                trim($file->getRelativePath(), DIRECTORY_SEPARATOR)
            );
        }
    }

    /**
     * @param PipeInterface $pipe
     *
     * @return self
     */
    public function pipe(PipeInterface $pipe)
    {
        $pipe->execute($this);

        return $this;
    }

    /**
     * Gets the value of distFiles.
     *
     * @return DistFile[] $distFiles
     */
    public function getDistFiles()
    {
        return $this->distFiles;
    }

    /**
     * @param int $key
     */
    public function removeDistFile($key)
    {
        unset($this->distFiles[$key]);
    }

    /**
     * @param DistFile $distFile
     */
    public function addDistFile(DistFile $distFile)
    {
        $this->distFiles[] = $distFile;
    }

    /**
     * Gets the value of dirs.
     *
     * @return SplFileInfo[] $dirs
     */
    public function getDirs()
    {
        return $this->dirs;
    }
}
