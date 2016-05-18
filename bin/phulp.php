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

$phulpFile = realpath('./PhulpFile.php');

if (file_exists($phulpFile)) {
    require $phulpFile;

    if (class_exists('PhulpFile')) {
        $phulp = new PhulpFile;

        if ($phulp instanceof Phulp\Phulp) {
            $phulp->define();
            $phulp->run($task);
        } else {
            die('PhulpFile Object MUST extends the \\Phulp\\Phulp.' . PHP_EOL);
        }
    } else {
        die('the file PhulpFile.php exists but the class name is not PhulpFile.' . PHP_EOL);
    }
} else {
    // @todo improve it
    die('The PhulpFile.php was not created.' . PHP_EOL);
}
