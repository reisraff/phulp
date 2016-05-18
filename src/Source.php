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
     * @param array $dirs
     * @param string|null $pattern
     * @param boolean $recursive
     */
    public function __construct(array $dirs, $pattern, $recursive)
    {
        $finder = new Finder;
        $finder->files();
        if (!$recursive) {
            $finder->depth('== 0');
        }
        if (!empty($pattern)) {
            $finder->name($pattern);
        }
        $finder->in($dirs);

        foreach ($finder as $file) {
            $this->distFiles[] = new DistFile(
                $file->getRelativePathname(), // See later PS: Maybe do in([]) separeted
                file_get_contents($file->getRealPath())
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
        $pipe->do($this);

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
}
