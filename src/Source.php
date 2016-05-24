<?php

namespace Phulp;

use Symfony\Component\Finder\Finder;

class Source
{
    /**
     * @var DistFile[] $distFiles;
     */
    private $distFiles = [];

    /**
     * @var \SplFileInfo[] $dirs;
     */
    private $dirs = [];

    /**
     * @param array $dirs
     * @param string|null $pattern
     * @param boolean $recursive
     */
    public function __construct(array $dirs, $pattern, $recursive)
    {
        $finder = new Finder;
        if (!$recursive) {
            $finder->depth('== 0');
        }
        if (!empty($pattern)) {
            $finder->name($pattern);
        }
        $finder->in($dirs);

        foreach ($finder as $file) {
            if ($file->isDir()) {
                $this->dirs[] = $file;
                continue;
            }

            $this->distFiles[] = new DistFile(
                file_get_contents($file->getRealPath()),
                substr($file->getRealPath(), strrpos($file->getRealPath(), DIRECTORY_SEPARATOR) + 1),
                substr($file->getRealPath(), 0, strrpos($file->getRealPath(), DIRECTORY_SEPARATOR)),
                trim($file->getRelativePath(), DIRECTORY_SEPARATOR)
            );
        }
    }

    /**
     * @param PipeInterface
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
     * @return DistFile[] $distFiles;
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
        if (isset($this->distFiles[$key])) {
            unset($this->distFiles[$key]);
        }
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
     * @return \SplFileInfo[] $dirs;
     */
    public function getDirs()
    {
        return $this->dirs;
    }
}
