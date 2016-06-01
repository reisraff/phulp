<?php

namespace Phulp;

class Watch
{
    /**
     * @param Source $src
     * @param callable $callback
     */
    public function __construct(Source $src, callable $callback)
    {
        while (true) {
            foreach ($src->getDistFiles() as $distFile) {
                if (!empty($distFile->getFullpath()) && file_exists($distFile->getFullpath())) {
                    clearstatcache();
                    $timeChange = filemtime(
                        rtrim($distFile->getFullpath(), DIRECTORY_SEPARATOR)
                        . DIRECTORY_SEPARATOR
                        . $distFile->getName()
                    );

                    if ($distFile->getLastChangeTime() < $timeChange) {
                        Output::out(
                            'The file "'
                            . rtrim($distFile->getRelativePath(), DIRECTORY_SEPARATOR)
                            . DIRECTORY_SEPARATOR
                            . $distFile->getName()
                            . '" was changed',
                            'yellow'
                        );
                        $distFile->setLastChangeTime($timeChange);
                        $callback();
                    }
                }
            }
            usleep(1);
        }
    }
}
