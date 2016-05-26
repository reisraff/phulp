<?php

foreach (['../../../autoload.php', '../../autoload.php', '../vendor/autoload.php', 'vendor/autoload.php'] as $autoload) {
    $autoload = __DIR__.'/'.$autoload;
    if (file_exists($autoload)) {
        require $autoload;
        break;
    }
}

ini_set('register_argc_argv', true);

$task = isset($argv[1]) ? $argv[1] : 'default';

$phulpFile = './PhulpFile.php';
if ( ! file_exists($phulpFile)) {
    Phulp\Output::out('The PhulpFile.php was not created.', 'red');
    return false;
}
require $phulpFile;

$phulp = new Phulp\Phulp();
$phulp->run($task);
