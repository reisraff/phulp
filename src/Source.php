<?php

namespace Phulp;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class Source
{
    /**
     * @var Collection|DistFile[] $distFiles
     */
    private $distFiles;

    /**
     * @param array $dirs
     * @param string $pattern
     * @param boolean $recursive
     *
     * @throws \UnexpectedValueException
     */
    public function __construct(array $dirs, $pattern = '', $recursive = false)
    {
        if (! count($dirs)) {
            throw new \UnexpectedValueException('There is no item in the array');
        }

        $finder = new Finder;

        if (!$recursive) {
            $finder->depth('== 0');
        }

        if ($pattern) {
            $finder->name($pattern);
        }

        $finder->in($dirs);

        $this->distFiles = new Collection([], DistFile::class);

        /** @var SplFileInfo $file */
        foreach ($finder as $file) {
            if ($file->isDir()) {
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
     * @param Collection|DistFile[] $distFiles
     *
     * @throws \UnexpectedValueException
     */
    public function setDistFiles(Collection $distFiles)
    {
        if ($distFiles->getType() !== DistFile::class) {
            throw new \UnexpectedValueException('The Collection is not of DistFile type');
        }

        $this->distFiles = $distFiles;
    }

    /**
     * Gets the value of distFiles.
     *
     * @return Collection|DistFile[] $distFiles
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
}
