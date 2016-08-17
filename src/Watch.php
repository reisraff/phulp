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
                            '[' . Output::colorize((new \DateTime())->format('H:i:s'), 'light_gray') . ']'
                            . ' The file "'
                            . Output::colorize(
                                rtrim($distFile->getRelativePath(), DIRECTORY_SEPARATOR)
                                . DIRECTORY_SEPARATOR
                                . $distFile->getName(),
                                'light_magenta'
                            )
                            . '" was changed'
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
