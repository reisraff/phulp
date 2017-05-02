<?php

$version = '1.8.1';

foreach (['../../../autoload.php', '../../autoload.php', '../vendor/autoload.php', 'vendor/autoload.php'] as $autoload) {
    $autoload = __DIR__.'/'.$autoload;
    if (file_exists($autoload)) {
        require $autoload;
        break;
    }
}

ini_set('register_argc_argv', true);

if (count($argv > 1)) {
    foreach ($argv as $key => $value) {
        if ($value == '-q' || $value == '--quiet') {
            Phulp\Output::$quiet = true;
            unset($argv[$key]);
            $argv = array_values($argv);
        }

        if ($value == '-V' || $value == '--version') {
            Phulp\Output::$quiet = false;
            Phulp\Output::out(
                Phulp\Output::colorize('Phulp', 'green')
                . ' version ' . Phulp\Output::colorize($version, 'yellow')
            );
            exit(0);
        }

        if ($value == '-h' || $value == '--help') {
            Phulp\Output::$quiet = false;

            Phulp\Output::out(
                '    ____  __          __    
   / __ \/ /_  __  __/ /___
  / /_/ / __ \/ / / / / __ \
 / ____/ / / / /_/ / / /_/ /
/_/   /_/ /_/\__,_/_/ .___/ 
                   /_/      ' . PHP_EOL
                . Phulp\Output::colorize('Phulp', 'green')
                    . ' version ' . Phulp\Output::colorize($version, 'yellow') . PHP_EOL . PHP_EOL
                . Phulp\Output::colorize('Usage:', 'yellow') . PHP_EOL
                . '  [task = default] [options]' . PHP_EOL . PHP_EOL
                . Phulp\Output::colorize('Options:', 'yellow') . PHP_EOL
                . Phulp\Output::colorize('  -h, --help', 'green')
                    . '              Display this help message' . PHP_EOL
                . Phulp\Output::colorize('  -q, --quiet', 'green')
                    . '             Do not output any message' . PHP_EOL
                . Phulp\Output::colorize('  -V, --version', 'green')
                    . '           Display this application version' . PHP_EOL
            );
            exit(0);
        }
    }
}

$phulpFiles = glob('[P,p]hulp[Ff]il{e,e.php}', GLOB_BRACE);

if (count($phulpFiles) > 1) {
    Phulp\Output::err(
        '[' . Phulp\Output::colorize((new \DateTime())->format('H:i:s'), 'light_gray') . '] '
        .  Phulp\Output::colorize('There are more than one Phulpfile present. ', 'red')
    );

    exit(1);
}

$phulpFile = array_pop($phulpFiles);

if (!isset($phulpFile)) {
    Phulp\Output::err(
        '[' . Phulp\Output::colorize((new \DateTime())->format('H:i:s'), 'light_gray') . '] '
        .  Phulp\Output::colorize('There\'s no Phulpfile present. ', 'red')
    );

    Phulp\Output::out(
        '[' . Phulp\Output::colorize((new \DateTime())->format('H:i:s'), 'light_gray') . '] '
        .  'Please check the documentation for proper Phulpfile naming. '
    );

    exit(1);
}

Phulp\Output::out(
    '[' . Phulp\Output::colorize((new \DateTime())->format('H:i:s'), 'light_gray') . '] '
    . 'Using Phulpfile ' . Phulp\Output::colorize(realpath($phulpFile) . ' ', 'light_magenta')
);

$phulp = new Phulp\Phulp();
require $phulpFile;

try {
    $phulp->run(isset($argv[1]) ? $argv[1] : 'default');
    unset($phulp);
} catch (\Exception $e) {
    Phulp\Output::err(
        '[' . Phulp\Output::colorize((new \DateTime())->format('H:i:s'), 'light_gray') . ']'
        . ' ' . Phulp\Output::colorize($e->getMessage(), 'light_red')
    );
    exit(1);
}
