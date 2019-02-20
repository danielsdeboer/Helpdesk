<?php

require 'vendor/autoload.php';

use Composer\XdebugHandler\XdebugHandler;

//Check if Xdebug is enabled and disable it.
$xdebug = new XdebugHandler('no-prefix');
$xdebug->check();
unset($xdebug);

// Unset the first argument, the name of the script.
unset($argv[0]);

$command = new PHPUnit\TextUI\Command();

/*
 * Run PHPUnit, passing all the arguments in.
 */
$command->run(
    array_merge(['phpunit'], $argv)
);
