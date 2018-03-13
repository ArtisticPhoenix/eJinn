<?php 
use evo\ejinn\eJinnParser;
use evo\ejinn\Exception\InvalidDataType;



if(isset($_GET['rebuild'])){
    $path = __DIR__.'/eJinnConf.php';
    echo "<pre>";
}else if(isset($_GET['config'])){
    $path = $_GET['config'];
    echo "<pre>";
}else if(isset($argv[1])){
    $path = $argv[1];
}

if(is_file($path)){
    $conf = require $path;
    if(!is_array($conf)) throw new InvalidDataType("Expected config as an array");
}

print_r(dirname($path));

$options = [
    'forceUnlock'       => true,
    'forceRecompile'    => true,
    'debug'             => ['dev','isCached','isLocked','showFiles'],
    //'createPaths'       => true,
    //'uniqueexceptions'  => false,
    //'parseOnly'  => true,
];

$Generator = new eJinnParser($conf, dirname($path), $options);
