<?php

namespace Phulp;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class Source
{
    /**
     * @var Collection::DistFile $distFiles
     */
    private $distFiles;

    /**
     * @var Collection::SplFileInfo $dirs
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

        $this->distFiles = new Collection([], DistFile::class);
        $this->dirs = new Collection([], SplFileInfo::class);

        /** @var SplFileInfo $file */
        foreach ($finder as $file) {
            if ($file->isDir()) {
                $this->dirs->add($file);
                continue;
            }

            $realPath = $file->getRealPath();
            $dsPos = strrpos($realPath, DIRECTORY_SEPARATOR);
            $this->distFiles->add(new DistFile(
                file_get_contents($realPath),
                substr($realPath, $dsPos + 1),
                substr($realPath, 0, $dsPos),
                trim($file->getRelativePath(), DIRECTORY_SEPARATOR)
            ));
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
        $this->distFiles->remove($key);
    }

    /**
     * @param DistFile $distFile
     */
    public function addDistFile(DistFile $distFile)
    {
        $this->distFiles->add($distFile);
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
