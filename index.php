<?php
use eJinn\eJinnParser;

$path = str_replace("\\", "/", __DIR__).'/';

require_once $path.'vendor/autoload.php';

echo "<pre>";

$conf = require $path.'examples/config/eJinn.php';

//print_r($conf);

$Generator = new eJinnParser($conf, $path."src/");

$Generator->build();


echo "\nComplete in ".__FILE__." on ".__LINE__;