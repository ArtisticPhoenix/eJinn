<?php
use eJinn\eJinnParser;
use eJinn\Exception\UnknownException;

//Throw new UnknownException();

$path = str_replace("\\", "/", __DIR__).'/';

require_once $path.'vendor/autoload.php';

echo "<pre>";

$conf = require $path.'examples/config/eJinn.php';

$options = [
    'forceUnlock'       => true,
    'forceRecompile'    => true,
    'debug'             => ['dev','isCached','isLocked'],
    'createPaths'       => true,
    'uniqueexceptions'  => false,
];

$Generator = new eJinnParser($conf, $path."src/", $options);


echo "\nComplete in ".__FILE__." on ".__LINE__;