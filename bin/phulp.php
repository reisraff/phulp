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

$phulpFile = './PhulpFile.php';
if ( ! file_exists($phulpFile)) {
    Phulp\Output::err('The PhulpFile.php was not created.');
    exit(1);
}

$phulp = new Phulp\Phulp();
require $phulpFile;
$phulp->run(isset($argv[1]) ? $argv[1] : 'default');
unset($phulp);
