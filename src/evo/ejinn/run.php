<?php 
use evo\ejinn\eJinnParser;
use evo\ejinn\Exception\InvalidDataType;

require_once __DIR__.'/../../../vendor/autoload.php';

if (isset($_GET['rebuild'])) {
    $path = __DIR__.'/eJinnConf.php';
    echo "<pre>";
} elseif (isset($_GET['config'])) {
    $path = $_GET['config'];
    echo "<pre>";
} elseif (isset($argv[1])) {
    $path = $argv[1];
}

if (is_file($path)) {
    $conf = require $path;
    if (!is_array($conf)) {
        throw new InvalidDataType("Expected config as an array");
    }
}

$options = [
    'forceUnlock'       => true,
    'forceRecompile'    => true,
    'debug'             => ['dev','isCached','isLocked','showFiles'],
    //'createPaths'       => true,
    //'uniqueexceptions'  => false,
    //'parseOnly'  => true,
];

$Generator = new eJinnParser($conf, dirname($path), $options);
