<?php 
use evo\ejinn\eJinnParser;
use evo\ejinn\exception\InvalidDataType;
use evo\ejinn\exception\InvalidConfigFile;

require_once EVO_AUTOLOAD;

if (isset($_GET['rebuild'])) {
    $path = __DIR__.'/eJinnConf.php';
    echo "<pre>";
} elseif (isset($_GET['config'])) {
    $path = $_GET['config'];
    echo "<pre>";
} elseif (isset($argv[1])) {
    $path = $argv[1];
} elseif (defined('EJINN_CONF_PATH')) {
    $path = EJINN_CONF_PATH;
}

if (is_file($path)) {
    $conf = require $path;
    if (!is_array($conf)) {
        throw new InvalidDataType("Expected config as an array");
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
    
}else{
    throw new InvalidConfigFile($path);
}


