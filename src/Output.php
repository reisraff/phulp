<?php

namespace Phulp;

class Output
{
    /**
     * The resource for out
     *
     * @var resource $out
     */
    protected static $out = STDOUT;

    /**
     * The resource for err
     *
     * @var resource $err
     */
    protected static $err = STDERR;

    /**
     *
     * @var boolean $quiet
     */
    public static $quiet = false;

    /**
     * @var array $color associative array of colors
     */
    private static $colors = [
        'default_foreground' => 39,
        'black' => 30,
        'red' => 31,
        'green' => 32,
        'yellow' => 33,
        'blue' => 34,
        'magenta' => 35,
        'cyan' => 36,
        'light_gray' => 37,
        'dark_gray' => 90,
        'light_red' => 91,
        'light_green' => 92,
        'light_yellow' => 93,
        'light_blue' => 94,
        'light_magenta' => 95,
        'light_cyan' => 96,
        'white' => 97,
    ];

    /**
     * This method is used to send some text to STDOUT, maybe with some color
     *
     * @param string $string the text to be sent to STDOUT
     * @param string $color the color to colorize your text
     *
     * @return void
     */
    public static function out($string)
    {
        if (! self::$quiet) {
            fwrite(
                static::$out,
                $string . PHP_EOL
            );
        }
    }

    /**
     * This method is used to send some text to STDERR
     *
     * @param string $string the text to be sent to STDERR
     *
     * @return void
     */
    public static function err($string)
    {
        if (! self::$quiet) {
            fwrite(
                static::$err,
                $string . PHP_EOL
            );
        }
    }

    /**
     * This method is used to colorize some text
     *
     * @param string $string the text to be colorized
     * @param string $color the color to colorize your
     *
     * @return string
     */
    public static function colorize($string, $color)
    {
        if (isset(self::$colors[$color])) {
            return "\033[" . self::$colors[$color] . 'm' . $string . "\033[0m";
        }

        return $string;
    }
}
