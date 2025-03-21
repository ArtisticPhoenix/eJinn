<?php
use evo\ejinn\eJinnParser;
use evo\exception as E;

require_once EVO_AUTOLOAD;
if(class_exists('\\evo\\debug\\Debug')){
    \evo\debug\Debug::regesterFunctions();
}

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

//$buildpath = $_GET['buildpath'] : filter_var($_GET['buildpath'], FILTER_FLAG_PATH_REQUIRED

if (is_file($path)) {
    $conf = require $path;
    if (!is_array($conf)) {
        throw new E\ResourceException("Invalid config file: $path");
    }

    $options = [
        'forceUnlock'       => $_GET['forceUnlock'] ?? true,
        'forceRecompile'    => $_GET['forceRecompile'] ?? true,
        'debug'             => $_GET['debug'] ?? true,
        'createPaths'       => $_GET['createPaths'] ?? false,
        'parseOnly'         => $_GET['parseOnly'] ?? false,
        'export'            => $_GET['export'] ?? false,
        'lockFile'          => $_GET['lockFile'] ?? 'ejinn.lock',
        'cacheFile'         => $_GET['cacheFile'] ?? 'ejinn.cache',
        'uniqueExceptions'  => $_GET['uniqueExceptions'] ?? true,
    ];

    new eJinnParser($conf, dirname($path), $options);
} else {
    die("Config file not found: ".$path);
}
