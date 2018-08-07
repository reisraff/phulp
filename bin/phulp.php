<?php

ini_set('register_argc_argv', true);

$version = '1.12.4';

$getArg = function ($arg, $isOption = true) use (&$argv) {
    if (count($argv) > 1) {
        foreach ($argv as $key => $value) {
            if ($isOption) {
                if ($arg === $value) {
                    unset($argv[$key]);
                    return true;
                }
            } else {
                if (preg_match('/^' . preg_quote($arg) . '(?:=.*+)?$/', $value)) {
                    unset($argv[$key]);

                    if ($arg === $value) {
                        if (isset($argv[$key + 1])) {
                            $val = $argv[$key + 1];
                            unset($argv[$key + 1]);
                            return $val;
                        }
                    } else {
                        return substr($value, strpos($value, '=') + 1);
                    }
                }
            }
        }
    }

    return false;
};

$files = ['../../../autoload.php', '../../autoload.php', '../vendor/autoload.php', 'vendor/autoload.php'];

foreach ($files as $autoload) {
    $autoload = realpath(__DIR__.DIRECTORY_SEPARATOR.$autoload);
    if (file_exists($autoload)) {
        require $autoload;
        break;
    }
}

if ($autoload = $getArg('--autoload', false)) {
    if (file_exists($autoload)) {
        require $autoload;
    } else {
        fwrite(
            STDERR,
            sprintf(
                "[%s] You have provided by argument an inexistent autoload file: %s.\n",
                (new \DateTime())->format('H:i:s'),
                $autoload
            )
        );
        exit(1);
    }
}

if (!class_exists(\Phulp\Phulp::class)) {
    fwrite(
        STDERR,
        sprintf(
            "[%s] Invalid Autoloading\n",
            (new \DateTime())->format('H:i:s')
        )
    );
    exit(1);
}

$out = \Phulp\Output::class;

if (count($argv) > 1) {
    if ($getArg('-V') || $getArg('--version')) {
        $out::$quiet = false;
        $out::out(
            $out::colorize('Phulp', 'green')
            . ' version ' . $out::colorize($version, 'yellow')
        );
        exit(0);
    }

    if ($getArg('-h') || $getArg('--help')) {
        $out::$quiet = false;

        $out::out(
                "    ____  __          __
   / __ \/ /_  __  __/ /___
  / /_/ / __ \/ / / / / __ \
 / ____/ / / / /_/ / / /_/ /
/_/   /_/ /_/\__,_/_/ .___/
                    /_/"
        );
        $out::out(sprintf(
           "%s version %s\n",
           $out::colorize('Phulp', 'green'),
           $out::colorize($version, 'yellow')
        ));
        $out::out(sprintf(
            "%s",
            $out::colorize('Usage:', 'yellow')
        ));
        $out::out(
            "  [task = default] [options] [arguments]\n"
        );
        $out::out(sprintf(
            "%s",
            $out::colorize('Arguments:', 'yellow')
        ));
        $out::out(sprintf(
            "%s              Add a alternative autoload php file",
            $out::colorize('  --autoload=/autoload/file.php', 'green')
        ));
        $out::out(sprintf(
            "%s                           Add a dinamic argument",
            $out::colorize('  --arg=name:value', 'green')
        ));
        $out::out(sprintf(
            "%s",
            $out::colorize('Options:', 'yellow')
        ));
        $out::out(sprintf(
            "%s                                 Display this help message",
            $out::colorize('  -h, --help', 'green')
        ));
        $out::out(sprintf(
            "%s                                Do not output any message",
            $out::colorize('  -q, --quiet', 'green')
        ));
        $out::out(sprintf(
            "%s                              Display this application version",
            $out::colorize('  -V, --version', 'green')
        ));
        exit(0);
    }

    if ($getArg('-q') || $getArg('--quiet')) {
        $out::$quiet = true;
        $argv = array_values($argv);
    }
}

if (defined('GLOB_BRACE')) {
    $phulpFiles = glob('[P,p]hulp[Ff]il{e,e.php}', GLOB_BRACE);
} else {
    $phulpFiles = [];
    $finder = \Symfony\Component\Finder\Finder::create()
        ->name('~^phulpfile(\.php)*$~i')->depth('< 1')->in(getcwd());
    foreach ($finder->getIterator() as $file) {
        $phulpFiles[] = $file->getFilename();
    }
}

if (count($phulpFiles) > 1) {
    $out::err(sprintf(
        '[%s] %s',
        $out::colorize((new \DateTime())->format('H:i:s'), 'light_gray'),
        $out::colorize('There are more than one Phulpfile present. ', 'red')
    ));
    exit(1);
}

$phulpFile = array_pop($phulpFiles);

if (!isset($phulpFile)) {
    $out::err(sprintf(
        '[%s] %s',
        $out::colorize((new \DateTime())->format('H:i:s'), 'light_gray'),
        $out::colorize('There\'s no Phulpfile present. ', 'red')
    ));

    $out::out(sprintf(
        '[%s] Please check the documentation for proper Phulpfile naming.',
        $out::colorize((new \DateTime())->format('H:i:s'), 'light_gray')
    ));
    exit(1);
}

$out::out(sprintf(
    '[%s] Using Phulpfile %s',
    $out::colorize((new \DateTime())->format('H:i:s'), 'light_gray'),
    $out::colorize(realpath($phulpFile), 'light_magenta')
));

$args = [];
while ($arg = $getArg('--arg', false)) {
    $arg = explode(':', $arg, 2);
    if (2 === count($arg)) {
        $args[$arg[0]] = $arg[1];
    }
}

$phulp = new Phulp\Phulp($args);
require $phulpFile;

try {
    $tasks = ! isset($argv[1]) ? [] : array_slice($argv, 1);
    $phulp->run($tasks);
    unset($phulp);
} catch (\Exception $e) {
    $out::err(sprintf(
        '[%s] %s',
        $out::colorize((new \DateTime())->format('H:i:s'), 'light_gray'),
        $out::colorize($e->getMessage(), 'light_red')
    ));
    exit(1);
}
