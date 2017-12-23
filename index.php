<?php
use eJinn\eJinnParser;

$path = str_replace("\\", "/", __DIR__).'/';

require_once $path.'vendor/autoload.php';

echo "<pre>";

$conf = require $path.'examples/config/eJinn.php';

$options = [
    'forceUnlock' => true,
    'debug' => ['dev', 'showEntities'],
    'createPaths' => true
];

$Generator = new eJinnParser($conf, $path."src/", $options);



echo "\nComplete in ".__FILE__." on ".__LINE__;