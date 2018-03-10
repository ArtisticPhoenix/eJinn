<?php
use evo\ejinn\eJinnParser;

echo "<pre>";

$conf = require_once __DIR__.'/eJinnConf.php';

$options = [
    'forceUnlock'       => true,
    'forceRecompile'    => true,
    'debug'             => ['dev','isCached','isLocked'],
    'createPaths'       => true,
    'uniqueexceptions'  => false,
];

$Generator = new eJinnParser($conf, __DIR__, $options);


echo "\nComplete in ".__FILE__." on ".__LINE__."</pre>";
