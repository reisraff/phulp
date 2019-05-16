<?php

namespace Phulp;

class Source
{
    /**
     * @var Collection|DistFile[] $distFiles
     */
    private $distFiles;

    /**
     * @param string $pattern
     *
     * @throws \UnexpectedValueException
     */
    public function __construct($pattern)
    {
        $this->distFiles = new Collection([], DistFile::class);

        foreach ($this->mglob($pattern) as $f) {
            $file = new \SplFileInfo($f);
            if ($file->isDir()) {
                continue;
            }

            $realPath = $file->getRealPath();
            $dsPos = strrpos($realPath, DIRECTORY_SEPARATOR);
            $this->distFiles->add(new DistFile(
                file_get_contents($realPath),
                substr($realPath, $dsPos + 1),
                substr($realPath, 0, $dsPos)
            ));
        }
    }

    private function mglob($pattern)
    {
        if (preg_match('/\*\*\//', $pattern)) {
            $explode = explode('**/', $pattern);
            $path = $explode[0];
            $find = $explode[1];

            $results = [];

            foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path)) as $x) {
                if (fnmatch($find, $x->getPathname())) {
                    $results[] = $x->getPathname();
                }
            }
        } else {
            $results = glob($pattern);
        }

        foreach ($results as $key => $value) {
            if (!is_file($value)) {
                unset($results[$key]);
            }
        }

        return array_values($results);
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
