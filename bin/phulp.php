<?php

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
    }
}

$phulpFiles = glob('[P,p]hulp[Ff]il{e,e.php}', GLOB_BRACE);

if (count($phulpFiles) > 1) {
    Phulp\Output::err(
        '[' . Phulp\Output::colorize((new \DateTime())->format('H:i:s'), 'light_gray') . '] '
        .  Phulp\Output::colorize(' There are more than one Phulpfile present. ', 'red')
    );

    exit(1);
}

$phulpFile = array_pop($phulpFiles);

if (!isset($phulpFile)) {
    Phulp\Output::err(
        '[' . Phulp\Output::colorize((new \DateTime())->format('H:i:s'), 'light_gray') . '] '
        .  Phulp\Output::colorize(' There\'s no Phulpfile present. ', 'red')
    );

    Phulp\Output::out(
        '[' . Phulp\Output::colorize((new \DateTime())->format('H:i:s'), 'light_gray') . '] '
        .  ' Please check the documentation for proper Phulpfile naming. '
    );

    exit(1);
}

Phulp\Output::out(
    '[' . Phulp\Output::colorize((new \DateTime())->format('H:i:s'), 'light_gray') . ']'
    . ' Using Phulpfile ' . Phulp\Output::colorize(realpath($phulpFile) . ' ', 'light_magenta')
);

$phulp = new Phulp\Phulp();
require $phulpFile;
$phulp->run(isset($argv[1]) ? $argv[1] : 'default');
unset($phulp);
